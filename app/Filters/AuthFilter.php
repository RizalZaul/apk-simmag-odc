<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    private const AUTH_MARKER_COOKIE = 'simmag_auth_marker';
    private const LOGOUT_MARKER_COOKIE = 'simmag_logout_marker';

    public function before(RequestInterface $request, $arguments = null)
    {
        $session  = session();
        $loggedIn = $session->get('logged_in');
        $role     = $session->get('role');
        $required = $arguments[0] ?? null;

        $isAjax = $request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';

        // ── Kasus guest ──────────────────────────────────
        if ($required === 'guest') {
            if ($loggedIn) {
                if ($isAjax) {
                    return $this->jsonRedirect(
                        $this->getRedirectUrl($role),
                        'Anda sudah login.'
                    );
                }
                return $this->redirectByRole($role);
            }
            return null;
        }

        // ── Belum login ──────────────────────────────────────────────────
        if (! $loggedIn) {
            $hasAuthMarker = false;
            $hasLogoutMarker = false;

            if ($request instanceof \CodeIgniter\HTTP\IncomingRequest) {
                $hasAuthMarker = $request->getCookie(self::AUTH_MARKER_COOKIE) !== null;
                $hasLogoutMarker = $request->getCookie(self::LOGOUT_MARKER_COOKIE) !== null;
            }

            $message = $hasLogoutMarker
                ? 'Silakan login terlebih dahulu.'
                : ($hasAuthMarker
                    ? 'Sesi Anda telah berakhir karena tidak aktif. Silakan login kembali.'
                    : 'Silakan login terlebih dahulu.');

            if ($isAjax) {
                return $this->jsonUnauthorized($message);
            }

            $response = redirect()
                ->to(base_url('auth/login'))
                ->with('error', $message);

            if ($hasAuthMarker) {
                $response->deleteCookie(self::AUTH_MARKER_COOKIE, '/');
            }
            if ($hasLogoutMarker) {
                $response->deleteCookie(self::LOGOUT_MARKER_COOKIE, '/');
            }

            return $response;
        }

        // ── Cek status akun secara real-time ke DB ───────────────────────
        $userId = $session->get('user_id');
        if ($userId) {
            $user = \Config\Database::connect()
                ->table('users')
                ->select('status')
                ->where('id_user', $userId)
                ->get()
                ->getRowArray();

            if (! $user || $user['status'] !== 'aktif') {
                $session->destroy();

                if ($isAjax) {
                    return $this->jsonUnauthorized('Akun Anda telah dinonaktifkan. Hubungi administrator.');
                }
                return redirect()
                    ->to(base_url('auth/login'))
                    ->with('error', 'Akun Anda telah dinonaktifkan. Hubungi administrator.')
                    ->deleteCookie(self::AUTH_MARKER_COOKIE, '/')
                    ->deleteCookie(self::LOGOUT_MARKER_COOKIE, '/');
            }
        }

        // ── Role mismatch ────────────────────────────────────────────────
        if ($required && $role !== $required) {
            if ($isAjax) {
                return $this->jsonRedirect(
                    $this->getRedirectUrl($role),
                    'Akses ditolak. Anda akan diarahkan ke dashboard.'
                );
            }
            return $this->redirectByRole($role);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        return null;
    }

    // ── Helpers ──────────────────────────────────────────────────────────

    private function redirectByRole(?string $role): \CodeIgniter\HTTP\RedirectResponse
    {
        return match ($role) {
            'admin' => redirect()->to(base_url('/')),
            'pkl'   => redirect()->to(base_url('pkl/dashboard')),
            default => redirect()->to(base_url('auth/login')),
        };
    }

    private function getRedirectUrl(?string $role): string
    {
        return match ($role) {
            'admin' => base_url('/'),
            'pkl'   => base_url('pkl/dashboard'),
            default => base_url('auth/login'),
        };
    }

    private function jsonUnauthorized(string $message): \CodeIgniter\HTTP\ResponseInterface
    {
        return \Config\Services::response()
            ->setStatusCode(401)
            ->setContentType('application/json')
            ->setJSON([
                'success'  => false,
                'message'  => $message,
                'redirect' => base_url('auth/login'),
            ]);
    }

    private function jsonRedirect(string $url, string $message = ''): \CodeIgniter\HTTP\ResponseInterface
    {
        return \Config\Services::response()
            ->setStatusCode(403)
            ->setContentType('application/json')
            ->setJSON([
                'success'  => false,
                'message'  => $message,
                'redirect' => $url,
            ]);
    }
}
