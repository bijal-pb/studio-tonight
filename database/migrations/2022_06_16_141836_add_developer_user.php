<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AddDeveloperUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $user = User::where("email", "ingeniousmindslab@gmail.com")->first();

        if (!$user) {
            $user = new User();
            $user->email  = "ingeniousmindslab@gmail.com";
            $user->name = "Developer";
            $user->password = bcrypt('iml@123456');
            $user->social_id = 'developer';
            $user->save();

            $role = Role::create(['name' => 'Developer']);
            $user->assignRole([$role->id]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
