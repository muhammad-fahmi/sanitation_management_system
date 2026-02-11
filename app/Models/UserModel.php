<?php

namespace App\Models;

use CodeIgniter\Model;
use Tatter\Audits\Traits\AuditsTrait;

/**
 * @see AuditsTrait
 */
class UserModel extends Model
{
    use AuditsTrait;
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'user_role',
        'username',
        'email',
        'password',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|alpha_numeric_space|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|max_length[255]|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[6]|max_length[255]',
        'is_active' => 'integer|in_list[0,1]',
    ];
    protected $validationMessages = [
        'username' => [
            'required' => 'The username is required.',
            'alpha_numeric_space' => 'The username can only contain letters, numbers, and spaces.',
            'min_length' => 'The username must be at least 3 characters long.',
            'max_length' => 'The username must be at most 100 characters long.',
            'is_unique' => 'The username is already taken.',
        ],
        'email' => [
            'required' => 'The email is required.',
            'valid_email' => 'The email is invalid.',
            'max_length' => 'The email must be at most 255 characters long.',
            'is_unique' => 'The email is already taken.',
        ],
        'password' => [
            'required' => 'The password is required.',
            'min_length' => 'The password must be at least 6 characters long.',
        ],
        'is_active' => [
            'in_list' => 'The is_active field must be either 0 or 1.',
        ],
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = ['auditInsert'];
    protected $beforeUpdate = [];
    protected $afterUpdate = ['auditUpdate'];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = ['auditDelete'];


    // Method For Checking (Validation)

    public function isUsernameExists(string $username): bool
    {
        return $this
            ->where('username', $username)
            ->countAllResults() > 0;
    }

    public function isUsernameUnique(string $username, int $id): bool
    {
        return $this
            ->where('username', $username)
            ->where('id !=', $id)
            ->countAllResults() > 0;
    }

    public function isEmailExists(string $email): bool
    {
        return $this
            ->where('email', $email)
            ->countAllResults() > 0;
    }

    // Method for retrieve data
    public function getUserByUsername(string $username): ?array
    {
        return $this
            ->builder('users as u')
            ->where('u.username', $username)
            ->get()
            ->getFirstRow('array');
    }
}
