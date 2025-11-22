<!DOCTYPE html>
<html lang="en">
@php
    $page = 'subscriptions';
    $common_files = 'main';
@endphp
@include('include.header', ['page' => $page, 'common_files' => $common_files])

@php
    $months = [
        1 => 'January', 2 => 'February', 3 => 'March',
        4 => 'April', 5 => 'May', 6 => 'June',
        7 => 'July', 8 => 'August', 9 => 'September',
        10 => 'October', 11 => 'November', 12 => 'December'
    ];

    $currentMonth = request('month', date('n'));
    $currentYear = request('year', date('Y'));
@endphp
<body>
    <section class="page-container">
        <div class="page-header">
            <i class="fi fi-rr-angle-small-left"></i>
            <div class="header-title">
                <p>Subscriptions</p>
            </div>
        </div>
        <div>
            <div id="subscriptionContent" class="mt-4">
                @include('../partials/subscriptions-content', [
                    'Subscriptions' => $subscriptions ?? [],
                ])
            </div>
        </div>
    </section>
</body>
<script>
    
    
</script>
</html>
