<?php
namespace Ishep\Security;
final class Csrf
{
    public function __construct(private Session $session) {}
    public function token(): string { $token=$this->session->get('_csrf'); if (!$token) { $token=bin2hex(random_bytes(32)); $this->session->put('_csrf',$token); } return $token; }
    public function valid(?string $token): bool { return is_string($token) && hash_equals($this->token(),$token); }
    public function rotate(): void { $this->session->put('_csrf',bin2hex(random_bytes(32))); }
}
