<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pricing Plans</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 40px 20px;
        }

        .btn-primary {
            --bs-btn-color: #fff;
            --bs-btn-bg: #1184a7;
            --bs-btn-border-color: #1184a7;
            --bs-btn-hover-color: #fff;
            --bs-btn-hover-bg: #0e6f8a;
            --bs-btn-hover-border-color: #0e6f8a;
            --bs-btn-focus-shadow-rgb: 49, 132, 253;
            --bs-btn-active-color: #fff;
            --bs-btn-active-bg: #1184a7;
            --bs-btn-active-border-color: #1184a7;
            --bs-btn-active-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.125);
            --bs-btn-disabled-color: #fff;
            --bs-btn-disabled-bg: #1184a7;
            --bs-btn-disabled-border-color: #1184a7;
        }

        .pricing-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .plan-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 25px 20px;
            height: 100%;
            transition: 0.3s ease;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .plan-card:hover {
            border: 2px solid #0e6f8a;
            box-shadow: 0 0 0 1px rgba(14, 111, 138, 0.2);
        }

        .toggle-button {
            border: 1px solid #ddd;
            border-radius: 30px;
            display: inline-flex;
            margin: 20px 0;
            overflow: hidden;
        }

        .toggle-button button {
            border: none;
            background: none;
            padding: 10px 20px;
            cursor: pointer;
            white-space: nowrap;
        }

        .toggle-button .active {
            background-color: #1184a7;
            color: white;
        }

        .toggle-button.single-button button {
            background-color: #1184a7;
            color: white;
        }

        .price {
            font-size: 2rem;
            font-weight: bold;
        }

        .price-note {
            font-size: 0.95rem;
            color: #6c757d;
        }

        .site-count {
            font-weight: 600;
            color: #1184a7;
            margin-bottom: 14px;
        }

        .tick-icon {
            background-color: #1184a7;
            color: white;
            display: inline-flex;
            width: 14px;
            height: 14px;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 8px;
            margin-right: 8px;
            flex-shrink: 0;
            position: relative;
            top: 2px;
        }

        .plan-actions {
            margin-top: auto;
            padding-top: 16px;
        }

        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .feature-text {
            flex: 1;
            line-height: 1.35;
        }

        @media (max-width: 767px) {
            .plan-card {
                margin-bottom: 20px;
            }

            .price {
                font-size: 1.6rem;
            }

            .toggle-button {
                flex-direction: column;
            }

            .toggle-button button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="mb-4">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary rounded-pill">
            Back to Dashboard
        </a>
    </div>

    <div class="pricing-header">
        <h4 class="fw-semibold">DigitMart Pricing Plans</h4>
        <p>Choose the plan that matches your category count and management needs.</p>
        @if($activePlan && $activePlan->is_visible)
            <p class="text-muted mb-2">Current active plan: <strong>{{ $activePlan->name }}</strong></p>
        @endif

        <div class="d-flex justify-content-center align-items-center mb-3 flex-wrap gap-2">
            <img src="https://randomuser.me/api/portraits/men/1.jpg" class="rounded-circle" width="35" style="margin-left: -15px;" height="35" alt="User">
            <img src="https://randomuser.me/api/portraits/women/2.jpg" class="rounded-circle" width="35" style="margin-left: -15px;" height="35" alt="User">
            <img src="https://randomuser.me/api/portraits/men/3.jpg" class="rounded-circle" width="35" style="margin-left: -15px;" height="35" alt="User">
            <img src="https://randomuser.me/api/portraits/women/4.jpg" class="rounded-circle" width="35" style="margin-left: -15px;" height="35" alt="User">
            <img src="https://randomuser.me/api/portraits/men/5.jpg" class="rounded-circle" width="35" style="margin-left: -15px;" height="35" alt="User">
            <img src="https://randomuser.me/api/portraits/women/6.jpg" class="rounded-circle" style="margin-left: -15px;" width="35" height="35" alt="User">
            <span class="ms-2">Join amazing creators & entrepreneurs</span>
        </div>

        <div class="d-flex justify-content-center flex-wrap align-items-center gap-2">
            <div class="toggle-button single-button">
                <button id="yearlyBtn" class="active">Yearly</button>
            </div>
            <span class="badge bg-success">20% off</span>
        </div>
    </div>

    <div class="row text-center" id="plansContainer"></div>
</div>

<script>
    const plans = @json($plans);

    function renderPlans() {
        const container = document.getElementById('plansContainer');
        container.innerHTML = '';

        plans.forEach(plan => {
            const featuresHTML = [`${plan.sites}`].concat(plan.features).map(feature =>
                `<li class="mb-2 feature-item"><span class="tick-icon">&#10004;</span><span class="feature-text">${feature}</span></li>`
            ).join('');

            const footerButton = `<div class="plan-actions"><a href="https://wa.me/918072515050" target="_blank" class="btn btn-primary w-100">Contact Support</a></div>`;

            container.innerHTML += `
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4 d-flex">
                    <div class="plan-card w-100">
                        <h5>${plan.title}</h5>
                        <p>${plan.desc}</p>
                        <div class="price">Rs. ${plan.yearly}/-</div>
                        <p class="price-note">Yearly plan</p>
                        <ul class="list-unstyled mt-3 text-start">
                            ${featuresHTML}
                        </ul>
                        ${footerButton}
                    </div>
                </div>`;
        });
    }

    renderPlans();
</script>

</body>
</html>
