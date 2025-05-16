use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


{
    public function up(): void
    {
        Schema::table('students_data', function (Blueprint $table) {
            $table->decimal('gpa', 3, 2)->nullable(); // Or whatever precision you need
            $table->integer('level')->nullable();
            $table->integer('total_credit_hours')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('students_data', function (Blueprint $table) {
            $table->dropColumn(['gpa', 'level', 'total_credit_hours']);
        });
    }
}