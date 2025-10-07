<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;
    public $table = 'role_permission';
    protected $fillable = [
        'permission_id',
        'role_id',
        'MasterEntry',
        'States',
        'City',
        'Price',
        'Area',
        'Career',
        'Testimonial',
        'Faq',
        'News_and_Updates',
        'Tags',
        'Vehicle',
        'Cms',
        'Goods_Type',
        'Our_Team',
        'Offer',
        'Driver_Request',
        'Driver_List',
        'Driver_Location',
        'Driver_Pass',
        'Seo',
        'Customer',
        'Employee_List',
        'Role',
        'Career_Inquiry',
        'Contact_Inquiry',
        'News_Letter_Inquiry',
        'istatus',
        'Isdelete',
        'created_at',
        'updated_at',
        'strIP'
    ];
}
