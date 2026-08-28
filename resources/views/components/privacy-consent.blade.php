@props(['id' => 'privacy_consent'])

@php
    $cndpNotice = "L'Université Euromed de Fès collecte vos données personnelles en vue de l'administration des étudiants. Ce traitement a fait l'objet d'une demande d'autorisation auprès de la CNDP sous le numéro A-GS-836/2022. Les données personnelles collectées peuvent être transmises au ministère de tutelle et aux organismes habilités conformément aux lois en vigueur. Elles peuvent également être transmises à la société CANTABO située en Allemagne pour la finalité d'hébergement conformément à la demande de transfert déposée auprès de la CNDP sous le numéro T-HB-331/2022. Vous pouvez vous adresser au Service Scolarité de l'Université Euromed de Fès (adresse mail : scolar@ueuromed.org) pour exercer vos droits d'accès, de rectification et d'opposition conformément aux dispositions de la loi 09-08.";
@endphp

<div class="privacy-consent rounded-3 border p-3 mb-4">
    <div class="form-check mb-0">
        <input
            id="{{ $id }}"
            name="privacy_consent"
            type="checkbox"
            value="1"
            class="form-check-input @error('privacy_consent') is-invalid @enderror"
            aria-describedby="{{ $id }}_notice {{ $id }}_errors"
            @checked(old('privacy_consent'))
            required
        >
        <label class="form-check-label" for="{{ $id }}">
            <span
                class="cndp-consent-message fw-semibold"
                tabindex="0"
                data-bs-toggle="tooltip"
                data-bs-trigger="hover focus"
                data-bs-placement="top"
                data-bs-custom-class="cndp-tooltip"
                data-bs-title="{{ $cndpNotice }}"
            >
                I consent to the processing of my personal data.
                <i class="bi bi-info-circle ms-1" aria-hidden="true"></i>
            </span>
        </label>
    </div>

    <span id="{{ $id }}_notice" class="visually-hidden">{{ $cndpNotice }}</span>

    <div id="{{ $id }}_errors" class="text-danger small mt-1 ms-4" role="alert">
        @foreach ($errors->get('privacy_consent') as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
</div>
