# PV de recrutement

Professors open **PV de recrutement** from their dashboard or sidebar, choose one of their subjects, complete the report, and save it or download it as Word. One report is stored per subject. Subject details and the complete applicant list are refreshed from the database when the form is opened or exported.

The committee contains 3–5 distinct people. Platform professors are resolved on the server; external members need a name and email. The same committee is used for preselection, interviews, and signature lines. A shortlist contains 1–5 applicants with scores from 0 to 100. Interview results contain 1–5 people from that shortlist, with interview scores from 0 to 100. Results are ranked by descending score, retaining entered order for ties. Saving requires both result stages to be complete; it does not change application admission statuses or send notifications.

The co-director is optional. When specified, provide a name and email. The report date is required. Scholarship choices, evaluation grids, institutional approvals, and signature spaces remain in the original template for completion in Word or on paper.

## Deployment

Include `PV-Recrutement-Canvas.docx` at the repository root in the deployed release (the exporter reads this exact filename). PHP must have the `zip` and `dom` extensions enabled and a writable system temporary directory. No additional Composer library is required.

```sh
php artisan migrate --force
npm ci
npm run build
php artisan optimize:clear
```

The migration creates `recruitment_reports` with a unique subject reference and report data. Build and deploy `public/build` when building assets outside the production server.

The exporter copies the DOCX package and modifies only its variable paragraphs and table rows. It leaves the original file untouched. If the template structure changes, update the mappings in `RecruitmentReportDocument` and its export tests together.

## Verification

```sh
php artisan test tests/Feature/RecruitmentReportTest.php
```

Tests cover subject ownership, invalid committee membership and counts, score boundaries, foreign applicants, interview shortlist membership, persistence, downloads, ranking, expandable tables, unchanged evaluation grids, and unchanged Word package parts.
