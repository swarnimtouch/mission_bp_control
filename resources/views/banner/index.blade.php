<!DOCTYPE html>
<html>
<head>
    <title>Doctor Banner Generator</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Croppie -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.css">

    <style>
        body {
            background: #f5f7fb;
        }

        .card {
            border-radius: 12px;
        }

        #cropper {
            width: 100%;
            height: 350px;
        }

        .preview-img {
            width: 140px;
            height: 140px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        /* 🔥 SCROLL FIX */
        .modal-body {
            max-height: 75vh;
            overflow-y: auto;
        }

        /* zoom slider styling */
        .cr-slider-wrap {
            margin-top: 15px;
        }
    </style>
    <style>
        .form-card {
            max-width: 700px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: .82rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* ── Shared input style ── */
        .form-group input {
            padding: 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            color: #0f172a;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
            background: #f8fafc;
            width: 100%;
            box-sizing: border-box;
        }

        .form-group input:focus {
            border-color: #38bdf8;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, .12);
        }

        .form-group input[readonly] {
            background: #f1f5f9;
            color: #94a3b8;
            cursor: not-allowed;
        }

        .form-group input.error {
            border-color: #f43f5e;
            background-color: #fff1f2;
        }

        /* ════════════════════════════════════
           SELECT2 — only for #doctor_id
           ════════════════════════════════════ */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 46px;
            padding: 0 40px 0 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a;
            font-size: 15px;
            font-family: inherit;
            line-height: 1;
            padding: 0;
            position: static;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #94a3b8;
            font-size: 15px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            height: auto;
            width: auto;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            border-color: #94a3b8 transparent transparent;
            border-width: 5px 4px 0;
            transition: border-color .2s;
        }

        .select2-container--default.select2-container--open
        .select2-selection--single .select2-selection__arrow b {
            border-color: transparent transparent #38bdf8;
            border-width: 0 4px 5px;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear {
            margin-right: 20px;
            color: #94a3b8;
            font-size: 16px;
            font-weight: 400;
            line-height: 1;
        }

        .select2-container--default .select2-selection--single .select2-selection__clear:hover {
            color: #f43f5e;
        }

        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #38bdf8;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, .12);
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #38bdf8;
            background-color: #fff;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        /* Error state for Select2 */
        .select2-error.select2-container--default .select2-selection--single {
            border-color: #f43f5e !important;
            background-color: #fff1f2 !important;
            box-shadow: none !important;
        }

        .select2-container--disabled .select2-selection--single {
            cursor: not-allowed !important;
            opacity: .55;
        }

        /* Dropdown panel */
        .select2-dropdown {
            border: 1.5px solid #38bdf8;
            border-top: none;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .1);
            background: #fff;
            overflow: hidden;
        }

        .select2-container--default .select2-search--dropdown {
            padding: 10px 12px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            width: 100%;
            padding: 8px 12px 8px 34px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            color: #0f172a;
            background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cline x1='21' y1='21' x2='16.65' y2='16.65'/%3E%3C/svg%3E") no-repeat 10px center;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: #38bdf8;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, .1);
        }

        .select2-results__options {
            max-height: 220px;
            overflow-y: auto;
            padding: 4px 0;
        }

        .select2-container--default .select2-results__option {
            padding: 10px 14px;
            font-size: 14px;
            font-family: inherit;
            color: #334155;
            transition: background .12s;
        }

        .select2-container--default
        .select2-results__option--highlighted.select2-results__option--selectable {
            background: #f0f9ff;
            color: #0284c7;
        }

        .select2-container--default .select2-results__option--selected {
            background: #e0f2fe;
            color: #0369a1;
            font-weight: 600;
        }

        .select2-container--default .select2-results__option.select2-results__message {
            color: #94a3b8;
            font-style: italic;
            font-size: 13px;
            text-align: center;
            padding: 16px;
        }

        /* ════════════════════════════════════
           NATIVE Custom Select — for #language
           ════════════════════════════════════ */
        .custom-select-wrap {
            position: relative;
            width: 100%;
        }

        .custom-select-wrap select {
            width: 100%;
            box-sizing: border-box;
            padding: 11px 44px 11px 14px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            color: #0f172a;
            background: #f8fafc;
            appearance: none;
            -webkit-appearance: none;
            outline: none;
            cursor: pointer;
            transition: border-color .2s, box-shadow .2s;
        }

        .custom-select-wrap select:focus {
            border-color: #38bdf8;
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(56, 189, 248, .12);
        }

        .custom-select-wrap select.error {
            border-color: #f43f5e;
            background-color: #fff1f2;
        }

        .custom-select-wrap select:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        /* Custom chevron arrow */
        .custom-select-wrap::after {
            content: '';
            pointer-events: none;
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid #94a3b8;
            transition: border-top-color .2s;
        }

        .custom-select-wrap:focus-within::after {
            border-top-color: #38bdf8;
        }

        /* ── Error messages ── */
        .err-msg {
            font-size: .78rem;
            color: #f43f5e;
            font-weight: 500;
            display: none;
        }

        .select-empty-msg {
            font-size: .82rem;
            color: #94a3b8;
            margin-top: 4px;
            display: none;
            font-style: italic;
        }

        /* ── Photo area ── */
        .photo-area {
            border: 2px dashed #cbd5e1;
            border-radius: 14px;
            padding: 24px;
            text-align: center;
            background: #f8fafc;
            transition: border .2s;
        }

        .photo-area:hover {
            border-color: #38bdf8;
        }

        .upload-label {
            display: inline-block;
            padding: 10px 22px;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            color: white;
            border-radius: 10px;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .photo-area p {
            color: #94a3b8;
            font-size: .82rem;
            margin-top: 8px;
        }

        /* ── Croppie ── */
        #crop-container {
            display: none;
            margin-top: 20px;
            text-align: center;
        }

        #crop {
            width: 300px;
            height: 300px;
            margin: 0 auto;
        }

        #crop-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 28px;
            background: #0f172a;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            touch-action: manipulation;
        }

        /* ── Preview ── */
        #preview-wrap {
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-top: 16px;
        }

        #preview-wrap img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #38bdf8;
        }

        #change-photo {
            background: none;
            border: none;
            color: #38bdf8;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            touch-action: manipulation;
        }

        /* ── Actions ── */
        .form-actions {
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .submit-btn {
            padding: 13px 32px;
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: opacity .2s;
            touch-action: manipulation;
        }

        .submit-btn:hover {
            opacity: .9;
        }

        .submit-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        .cancel-link {
            color: #64748b;
            font-size: .88rem;
            text-decoration: none;
            font-weight: 500;
        }

        /* ── MOBILE ── */
        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: 1;
            }

            #crop {
                width: 260px;
                height: 260px;
            }

            .photo-area {
                padding: 18px 14px;
            }

            .submit-btn {
                width: 100%;
                text-align: center;
            }

            .form-actions {
                flex-direction: column;
                gap: 10px;
            }

            .cancel-link {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="card p-4 shadow">
        <h3 class="mb-4">Doctor Banner Generator</h3>

        <form method="POST" action="{{ route('generate.banner') }}">
            @csrf

            <div class="mb-3">
                <label>Doctor Name</label>
                <input type="text" name="doctor_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Speciality</label>
                <input type="text" name="speciality" class="form-control" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Time</label>
                    <input type="time" name="time" class="form-control" required>
                </div>
            </div>

            <div class="form-group full">
                <label>Doctor Photo *</label>

                <div class="photo-area" id="photoArea">
                    <label for="upload" class="upload-label">📷 Choose Photo</label>
                    <input type="file" id="upload" accept="image/*" style="display:none">
                    <p>JPG, PNG supported • Photo will be cropped to circle</p>
                </div>
                <span class="err-msg" id="err_photo">Please upload and crop a photo.</span>

                <div id="crop-container">
                    <div id="crop"></div>
                    <button type="button" id="crop-btn">✂️ Crop &amp; Use Photo</button>
                </div>

                <div id="preview-wrap">
                    <img id="preview-img" src="" alt="Preview">
                    <button type="button" id="change-photo">🔄 Change Photo</button>
                </div>
            </div>

            <input type="hidden" name="cropped_image" id="cropped_image">

            <button class="btn btn-primary w-100">Generate & Download ZIP</button>
        </form>
    </div>
</div>

<!-- Crop Modal -->
<div class="modal fade" id="cropModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content p-3">

            <div class="modal-header">
                <h5 class="modal-title">Crop Doctor Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <div id="cropper"></div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" id="cropBtn">Crop & Use</button>
            </div>

        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/croppie/2.6.5/croppie.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var crop = null;
    var photoCropped = false;

    $(document).ready(function () {

        // Upload → Croppie bind
        $('#upload').on('change', function () {
            var file = this.files[0];
            if (!file) return;

            var reader = new FileReader();
            reader.onload = function (e) {
                $('#photoArea').hide();
                $('#preview-wrap').hide().css('display', 'none');
                $('#crop-container').show();

                if (crop) {
                    crop.croppie('destroy');
                    crop = null;
                }

                var isMobile = window.innerWidth <= 600;
                crop = $('#crop').croppie({
                    viewport: {width: isMobile ? 180 : 200, height: isMobile ? 180 : 200, type: 'circle'},
                    boundary: {width: isMobile ? 260 : 300, height: isMobile ? 260 : 300}
                });

                crop.croppie('bind', {url: e.target.result});
                photoCropped = false;
            };
            reader.readAsDataURL(file);
        });

        // Crop button → base64 save + preview
        $('#crop-btn').on('click', function () {
            if (!crop) return;
            crop.croppie('result', 'base64').then(function (img) {
                $('#cropped_image').val(img);
                $('#preview-img').attr('src', img);
                $('#crop-container').hide();
                $('#preview-wrap').css('display', 'flex');
                photoCropped = true;
                $('#err_photo').hide();
            });
        });

        // Change photo → reset
        $('#change-photo').on('click', function () {
            $('#preview-wrap').hide();
            $('#photoArea').show();
            $('#cropped_image').val('');
            $('#upload').val('');
            photoCropped = false;
        });

        // Form submit → only photo validation
        $('form').on('submit', function (e) {
            if (!photoCropped || !$('#cropped_image').val()) {
                $('#err_photo').show();
                e.preventDefault();
                return false;
            }
            $('#err_photo').hide();
        });

    });
</script>


</body>
</html>
