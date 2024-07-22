<?php

namespace App\Models\chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Exception;
use App\Http\Controllers\responseController;
use App\Models\paginationModel;
use Illuminate\Support\Facades\DB;

class usersModel extends Model
{
    use HasFactory;

    protected $fillable = [];
    //List data users
    public static function indexDataUser($request)
    {
        DB::beginTransaction();
        try {
            $condition = null;
            $show      = $request->show;
            $page      = $request->page;

            // search
            $search = "";
            if (!empty($request->search)) {
                $search = " AND (us.id ILIKE ('%$request->search%')
                            OR   us.name ILIKE ('%$request->search%')
                            OR   us.email ILIKE ('%$request->search%')) ";
            } else {
                $search = "";
            }
            $group = null;

            // query
            $query = "";
            $query = "  SELECT
                            us.id,
                            us.name,
                            us.email,
                            us.password,
                            us.active_status,
                            us.avatar,
                            us.dark_mode,
                            us.messenger_color,
                            CONCAT(mu.last_name_en, ' ', mu.first_name_en) AS created_by,
                            TO_CHAR(us.created_at + INTERVAL '7 hour', 'dd-mm-yyyy') AS created_date
                        FROM
                            users us
                        LEFT JOIN ma_user mu ON mu.id = us.created_by";
            $sort  = "";
            $sort  = " ORDER BY us.id ASC ";

            return paginationModel::pagination($query, $show, $condition, $group, $sort, $page, $search);
        } catch (Exception $e) {
            DB::rollBack();
            return responseController::error($e->getMessage());
        }
    }
    //Store Data Users
    public static function storeDataUsers($request){
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Validation and default values
            $createdBy         = $request->created_by ?? '1';
            $name              = $request->name ?? '';
            $email             = $request->email ?? '';
            $email_verified_at = $request->email_verified_at ?? null;  // Note: null if not set
            $password          = $request->password ?? '';
            $remember_token    = $request->remember_token ?? null;  // Note: null if not set
            $created_at        = now();
            $updated_at        = now();
            $active_status     = $request->active_status ?? false;
            $avatar            = $request->avatar ?? 'avatar.png';
            $dark_mode         = $request->dark_mode ?? false;
            $messenger_color   = $request->messenger_color ?? null;  // Note: null if not set

            // Insert using the stored procedure
            $insertUsers = DB::select("SELECT public.insert_users(?,?,?,?,?,?,?,?,?,?,?,?)",
            [
                $name,
                $email,
                $email_verified_at,
                $password,
                $createdBy,
                $remember_token,
                $created_at,
                $updated_at,
                $active_status,
                $avatar,
                $dark_mode,
                $messenger_color
            ]);

            DB::commit();
            return responseController::success($insertUsers);
        } catch(Exception $ex) {
            DB::rollBack();
            return responseController::error($ex->getMessage());
        }
    }
}
