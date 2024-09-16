<?php

namespace App\Models;

use App\Events\CustomerModelBootedEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * @property int id
 * @property string first_name
 * @property string last_name
 * @property string description
 * @property int author
 * @property string gender
 * @property string phone
 * @property string email
 * @property string pobox
 * @property int group_id
 * @property string birth_date
 * @property float purchases_amount
 * @property float owed_amount
 * @property float credit_limit_amount
 * @property float account_amount
 */
class Branch extends Model
{
    use HasFactory;

    protected $table = 'nexopos_' . 'branches';

}
