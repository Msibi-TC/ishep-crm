<?php
namespace Ishep\Services;
use PDO;use Ishep\Repositories\UserRepository;use Ishep\Repositories\RoleRepository;
final class RegistrationService { public function __construct(private PDO $db,private UserRepository $users,private RoleRepository $roles){} public function register(string $name,string $email,string $password):int{$this->db->beginTransaction();try{$id=$this->users->create($name,$email,password_hash($password,PASSWORD_DEFAULT));$this->roles->assignRegistered($id);$this->db->commit();return$id;}catch(\Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw$e;}} }
