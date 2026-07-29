<?php

namespace App\Enums;

enum DocumentType: string
{
    case CinPassport = 'cin_passport';
    case Cv = 'cv';
    case MotivationLetter = 'motivation_letter';
    case Transcript = 'transcript';
    case LastDiplomaCertificate = 'last_diploma_certificate';
    case LicenseCertificate = 'license_certificate';
    case BaccalaureatCertificate = 'baccalaureat_certificate';

    /** @deprecated Retired generic diploma bucket, kept only to render historical documents. */
    case Diploma = 'diploma';

    /** @deprecated Retired from all flows, kept only to render historical documents. */
    case ResearchProposal = 'research_proposal';

    case Publication = 'publication';
    case RecommendationLetter = 'recommendation_letter';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CinPassport => 'National ID / Passport',
            self::Cv => 'Curriculum Vitae (CV)',
            self::MotivationLetter => 'Motivation Letter',
            self::Transcript => 'Academic Transcript',
            self::LastDiplomaCertificate => 'Last Diploma Certificate',
            self::LicenseCertificate => 'License / Bachelor Certificate',
            self::BaccalaureatCertificate => 'Baccalaureat Certificate',
            self::Diploma => 'Diploma',
            self::ResearchProposal => 'Research Proposal',
            self::Publication => 'Publication',
            self::RecommendationLetter => 'Recommendation Letter',
            self::Other => 'Other Document',
        };
    }

    /**
     * @deprecated Superseded by profileRequired()/profileOptional()/forApplication() for new flows.
     */
    public function isRequired(): bool
    {
        return match ($this) {
            self::Publication, self::RecommendationLetter, self::Other => false,
            default => true,
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::CinPassport => 'bi-person-vcard',
            self::Cv => 'bi-file-earmark-person',
            self::MotivationLetter => 'bi-envelope-paper',
            self::Transcript => 'bi-file-earmark-bar-graph',
            self::LastDiplomaCertificate => 'bi-award',
            self::LicenseCertificate => 'bi-mortarboard',
            self::BaccalaureatCertificate => 'bi-patch-check',
            self::Diploma => 'bi-award',
            self::ResearchProposal => 'bi-file-earmark-richtext',
            self::Publication => 'bi-journal-bookmark',
            self::RecommendationLetter => 'bi-file-earmark-person',
            self::Other => 'bi-file-earmark',
        };
    }

    /**
     * @return array<self>
     */
    public static function required(): array
    {
        return array_values(array_filter(self::cases(), fn (self $type) => $type->isRequired()));
    }

    /**
     * @return array<self>
     */
    public static function optional(): array
    {
        return array_values(array_filter(self::cases(), fn (self $type) => ! $type->isRequired()));
    }

    /**
     * Document(s) collected in the profile's Personal Information section.
     *
     * @return array<self>
     */
    public static function personalDocuments(): array
    {
        return [self::CinPassport];
    }

    /**
     * Documents always required in the profile's Academic Background
     * section, regardless of degree track. The License certificate is
     * intentionally excluded here — its requirement depends on the
     * candidate's chosen degree track and is handled in Profile directly.
     *
     * @return array<self>
     */
    public static function academicRequiredDocuments(): array
    {
        return [self::LastDiplomaCertificate, self::BaccalaureatCertificate, self::Transcript, self::Cv];
    }

    /**
     * Optional documents collected once on the candidate's profile.
     *
     * @return array<self>
     */
    public static function academicOptionalDocuments(): array
    {
        return [self::Publication, self::RecommendationLetter, self::Other];
    }

    /**
     * Documents collected fresh for each subject application.
     * Research proposal is permanently retired from all new flows.
     *
     * @return array<self>
     */
    public static function forApplication(): array
    {
        return [self::MotivationLetter];
    }
}
