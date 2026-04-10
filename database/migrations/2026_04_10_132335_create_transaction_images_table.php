<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_images', function (Blueprint $table) {
            $table->id();
            $table->string('path'); // Percorso del file immagine
            $table->string('original_name'); // Nome originale del file
            $table->string('mime_type'); // Tipo MIME dell'immagine
            $table->unsignedBigInteger('size'); // Dimensione in bytes
            $table->enum('transaction_type', ['expense', 'credit']); // Tipo di transazione
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->text('extracted_text')->nullable(); // Testo estratto dall'OCR
            $table->decimal('extracted_amount', 10, 2)->nullable(); // Importo estratto
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->date('extracted_date')->nullable(); // Data estratta
            $table->string('extracted_description')->nullable(); // Descrizione estratta
            $table->boolean('is_processed')->default(false); // Se l'OCR è stato elaborato
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_images');
    }
};
