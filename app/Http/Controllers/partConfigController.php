<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class partConfigController extends Controller
{
        //
        public static function stock_img_path(){
            return '/media/file/stock/img/';
        }
        public static function profile_img_path(){
            return '/media/file/main_app/profile/img/';
        }
        public static function crm_lead_file_path(){
            return '/media/file/crm/lead/';
        }
        public static function uploadfile(){
            return '/media/file/document/';
        }
        public static function abc(){

        }
        public static function img_en($st){
            // dump($st);
            $extension = pathinfo($st, PATHINFO_EXTENSION);
            $st=md5(time().$st).'.'.$extension;
            return $st;
        }

        public static function Move_Upload($fileMove,$path){
            if($fileMove== null){
                return false;
            }
            $filename = $fileMove->getClientOriginalName();
            $url_path = public_path($path); //path for move
            if (!file_exists($url_path)) {
                mkdir($url_path, 0777, true);
            }
            $renamefile= partConfigController::img_en($filename);
            $uploadfile = $url_path.$renamefile;
            $filedirectory = $path.$renamefile;
            if (move_uploaded_file($fileMove, $uploadfile)) {
                return $filedirectory;
            } else {
                return false;
            }
        }

        //Upload file and insert to table ma_uploaded_file return id of this table
        // public static function insertUploadedFile($file,$path,$create_by){
        //     $FilePath=self::Move_Upload($file,$path);
        //     if(!$FilePath&&!is_object($file)){
        //         return null;
        //     }
        //     DB::beginTransaction();
        //     try {
        //         $result= DB::selectOne("SELECT public.insert_ma_uploaded_files(?,?,?,?) as id",[$FilePath,$create_by,''.$file->getClientOriginalName(),''.$file->getClientOriginalExtension()]);
        //         // dd($FilePath,$create_by,''.$file->getClientOriginalName(),''.$file->getClientOriginalExtension());
        //         DB::commit();
        //         return $result->id;
        //     } catch(\Exception $e){
        //         DB::rollback();
        //         throw $e;
        //     }
        // }

        public static function insertUploadedFile($fileName,$path,$extension,$create_by){
            // $FilePath=self::Move_Upload($file,$path);
            // if(!$FilePath&&!is_object($file)){
            //     return null;
            // }
            DB::beginTransaction();
            try {
                $result= DB::selectOne("SELECT public.insert_web_uploaded_files(?,?,?,?) as id",[$path,$create_by,''.$fileName,''.$extension]);
                // dd($FilePath,$create_by,''.$file->getClientOriginalName(),''.$file->getClientOriginalExtension());
                DB::commit();
                return $result->id;
            } catch(Exception $e){
                DB::rollback();
                throw $e;
            }
        }
        public static function insertWebUploadedFile($fileName,$path,$extension,$createby){
            // $FilePath=self::Move_Upload($file,$path);
            // if(!$FilePath&&!is_object($file)){
            //     return null;
            // }
            DB::beginTransaction();
            try {
                $result= DB::connection('website')->selectOne("SELECT public.insert_web_upload_files(?,?,?,?) as id",[$path,''.$fileName,''.$extension,$createby]);
                // dd($FilePath,$create_by,''.$file->getClientOriginalName(),''.$file->getClientOriginalExtension());
                DB::commit();
                return $result->id;
            } catch(\Exception $e){
                DB::rollback();
                throw $e;
            }
        }
        public static function insertAssetUploadedFile($fileName,$path,$extension,$createby)
        {
            DB::beginTransaction();
            try
            {
                $result= DB::selectOne("SELECT public.insert_ast_upload_files(?,?,?,?) as id",[$path,''.$fileName,''.$extension,$createby]);
                DB::commit();
                return $result->id;
            }
            catch(Exception $e)
            {
                DB::rollback();
                throw $e;
            }
        }
}
