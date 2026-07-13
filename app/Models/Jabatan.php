<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'data_jabatan';
    protected $primaryKey = 'id_jabatan';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['id_jabatan', 'nama_jabatan'];

    public function employees()
    {
        return $this->hasMany(EmployeeSalary::class, 'id_jabatan', 'id_jabatan');
    }
}
