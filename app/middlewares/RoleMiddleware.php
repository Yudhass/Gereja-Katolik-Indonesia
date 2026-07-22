<?php

require_once dirname(__FILE__) . '/../core/Middleware.php';

/**
 * Role Middleware
 * Cek apakah user memiliki role tertentu
 */
class RoleMiddleware extends Middleware
{
    private $allowedRoles = array();
    
    /**
     * Constructor
     * 
     * @param array|string $roles Role yang diizinkan
     */
    public function __construct($roles = array())
    {
        if (is_string($roles)) {
            $this->allowedRoles = array($roles);
        } else {
            $this->allowedRoles = $roles;
        }
    }
    
    public function handle()
    {
        // Cek apakah user sudah login
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            $this->redirect('login', 'Silakan login terlebih dahulu.', 'warning');
            return false;
        }
        
        // Get user role dari session atau database
        $userRole = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : null;
        
        // Jika tidak ada role di session, ambil dari database
        if (!$userRole && isset($_SESSION['user']['id'])) {
            require_once dirname(__FILE__) . '/../models/ModelUser.php';
            $User = new ModelUser();
            $userList = $User->selectWhere('id', $_SESSION['user']['id']);
            
            if ($userList && count($userList) > 0) {
                // Keep as object for PHP 8+ compatibility (no array casting)
                $user = $userList[0];
                $userRole = isset($user->role) ? $user->role : null;
                
                // Update session dengan role
                if ($userRole) {
                    $_SESSION['user']['role'] = $userRole;
                }
            }
        }
        
        // Cek apakah user memiliki role yang diizinkan
        if (!$userRole || !in_array($userRole, $this->allowedRoles)) {
            $allowedRolesStr = implode(', ', $this->allowedRoles);
            $this->forbidden(
                'Access denied. This page requires one of these roles: ' . $allowedRolesStr . 
                '. Your role: ' . ($userRole ? $userRole : 'None')
            );
            return false;
        }
        
        return true;
    }
}
