<?php

namespace Database\Factories;

use App\Enums\LetterType;
use App\Models\Letter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Letter>
 */
class LetterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'reference_number' => $this->faker->unique()->ean13(),
        'letter_nature' => $this->faker->randomElement(['Segera', 'Sangat Segera', 'Biasa', 'Rahasia', 'Sangat Rahasia']),
        'agenda_number' => strtoupper($this->faker->bothify('AGD-####')),
        'from' => $this->faker->name('male'),
        'to' => $this->faker->name('female'),
        'letter_date' => $this->faker->date(),
        'received_date' => $this->faker->date(),
        'description' => $this->faker->sentence(7),
        'note' => $this->faker->sentence(3),
        'type' => $this->faker->randomElement([
            LetterType::INCOMING->type(),
            LetterType::OUTGOING->type()
        ]),
        'classification_code' => 'ADM', // asumsi kode ini fix/ada di DB
        'sub_classification_id' => 1,   // pastikan ID ini ada, atau pakai factory kalau ada relasi
        'user_id' => 1,                 // pastikan user ini ada juga
        'bidang' => $this->faker->randomElement(['Umum', 'Kepegawaian', 'Keuangan']),
        'letter_code' => strtoupper($this->faker->bothify('LTR-###')),
    ];
}
}
