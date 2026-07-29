<?php

namespace App\Models;

use App\Enums\DegreeTrack;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'birth_date',
        'birth_place',
        'nationality',
        'gender',
        'cin_or_passport_number',
        'address',
        'phone',
        'degree_track',
        'last_degree',
        'last_institution',
        'graduation_year',
        'field_of_study',
        'grade_mention',
        'license_institution',
        'license_graduation_year',
        'license_field_of_study',
        'license_grade_mention',
        'bac_institution',
        'bac_graduation_year',
        'bac_field_of_study',
        'bac_grade_mention',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'degree_track' => DegreeTrack::class,
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProfileDocument::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    /**
     * The License/Bachelor's tier is only required for candidates on the
     * Master track — Engineer-track candidates may skip it entirely.
     */
    public function licenseRequired(): bool
    {
        return $this->degree_track === DegreeTrack::Master;
    }

    public function hasDocument(DocumentType $type): bool
    {
        return $this->documents->contains(fn (ProfileDocument $document) => $document->type === $type);
    }

    public function personalComplete(): bool
    {
        $fieldsFilled = collect(array_keys(self::personalFields()))->every(fn (string $field) => filled($this->{$field}));

        return $fieldsFilled && $this->hasDocument(DocumentType::CinPassport);
    }

    public function academicComplete(): bool
    {
        if (blank($this->degree_track)) {
            return false;
        }

        $lastDiplomaFilled = collect(array_keys(self::lastDiplomaRequiredFields()))->every(fn (string $field) => filled($this->{$field}));
        $bacFilled = collect(array_keys(self::bacRequiredFields()))->every(fn (string $field) => filled($this->{$field}));
        $licenseFilled = ! $this->licenseRequired()
            || collect(array_keys(self::licenseRequiredFields()))->every(fn (string $field) => filled($this->{$field}));

        $documentsFilled = collect(DocumentType::academicRequiredDocuments())
            ->every(fn (DocumentType $type) => $this->hasDocument($type));
        $licenseDocumentFilled = ! $this->licenseRequired() || $this->hasDocument(DocumentType::LicenseCertificate);

        return $lastDiplomaFilled && $bacFilled && $licenseFilled && $documentsFilled && $licenseDocumentFilled;
    }

    public function isComplete(): bool
    {
        return $this->personalComplete() && $this->academicComplete();
    }

    /**
     * @return array<int, string>
     */
    public function missingRequirements(): array
    {
        $missing = [];

        foreach (self::personalFields() as $field => $label) {
            if (blank($this->{$field})) {
                $missing[] = $label;
            }
        }

        if (! $this->hasDocument(DocumentType::CinPassport)) {
            $missing[] = DocumentType::CinPassport->label();
        }

        if (blank($this->degree_track)) {
            $missing[] = 'Degree track (Master or Engineer)';
        }

        foreach (self::lastDiplomaRequiredFields() as $field => $label) {
            if (blank($this->{$field})) {
                $missing[] = $label;
            }
        }

        foreach (self::bacRequiredFields() as $field => $label) {
            if (blank($this->{$field})) {
                $missing[] = $label;
            }
        }

        if ($this->licenseRequired()) {
            foreach (self::licenseRequiredFields() as $field => $label) {
                if (blank($this->{$field})) {
                    $missing[] = $label;
                }
            }
        }

        foreach (DocumentType::academicRequiredDocuments() as $type) {
            if (! $this->hasDocument($type)) {
                $missing[] = $type->label();
            }
        }

        if ($this->licenseRequired() && ! $this->hasDocument(DocumentType::LicenseCertificate)) {
            $missing[] = DocumentType::LicenseCertificate->label();
        }

        return $missing;
    }

    /**
     * @return array<string, string>
     */
    public static function personalFields(): array
    {
        return [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'birth_date' => 'Date of birth',
            'birth_place' => 'Place of birth',
            'nationality' => 'Nationality',
            'gender' => 'Gender',
            'cin_or_passport_number' => 'National ID / Passport number',
            'address' => 'Home address',
            'phone' => 'Phone number',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function lastDiplomaRequiredFields(): array
    {
        return [
            'last_degree' => 'Last degree obtained',
            'last_institution' => 'Institution (Last Diploma)',
            'graduation_year' => 'Graduation year (Last Diploma)',
            'field_of_study' => 'Field of study (Last Diploma)',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function licenseRequiredFields(): array
    {
        return [
            'license_institution' => 'Institution (License)',
            'license_graduation_year' => 'Graduation year (License)',
            'license_field_of_study' => 'Field of study (License)',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function bacRequiredFields(): array
    {
        return [
            'bac_institution' => 'Institution (Baccalaureat)',
            'bac_graduation_year' => 'Graduation year (Baccalaureat)',
            'bac_field_of_study' => 'Field of study (Baccalaureat)',
        ];
    }
}
