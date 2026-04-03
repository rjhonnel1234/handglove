<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table = 'tbl_messages';

    protected $allowedFields = [
        'message_uuid',
        'from_number',
        'to_number',
        'message',
        'channel',
        'direction',
        'status'
    ];

    protected $useTimestamps = false;
}
