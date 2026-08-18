<?php
namespace Ishep\Services;
use Ishep\Repositories\RoleRepository;
final class AuthorizationService { public function __construct(private RoleRepository $roles){} public function hasRole(int $id,string $role):bool{return in_array($role,$this->roles->roles($id),true);} public function can(int $id,string $permission):bool{return in_array($permission,$this->roles->permissions($id),true);} }
