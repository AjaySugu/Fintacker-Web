<!DOCTYPE html>
<html lang="en">
@php
    $page = 'budgets';
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
                <p>Monthly Budget</p>
            </div>
        </div>
        <div>
            <div class="month-selection-container">
                <i class="fi fi-rr-angle-small-left" id="prevMonthBtn" style="cursor:pointer;"></i>
                <div class="header-title budget-t text-center">
                    <p class="mb-0 fw-bold" id="monthLabel">{{ $months[$currentMonth] }} {{ $currentYear }}</p>
                </div>
                <i class="fi fi-rr-angle-small-right" id="nextMonthBtn" style="cursor:pointer;"></i>
            </div>
            <!-- <h5 class="budget-title-mwise">
                Showing Budget for 
                <span class="text-primary" id="currentMonthText">{{ $months[$currentMonth] }} {{ $currentYear }}</span>
            </h5> -->
            <div id="budgetContent" class="mt-4">
                @include('../partials/budget-content', [
                    'budgetData' => $budgetData ?? [],
                    'month' => $currentMonth,
                    'year' => $currentYear
                ])
            </div>
        </div>
    </section>
</body>
<script>
    let currentMonth = {{ $currentMonth }};
    let currentYear = {{ $currentYear }};
    const months = @json($months);

    // Function to update content dynamically
    function loadBudget(month, year) {
        fetch(`{{ route('budget.index') }}?month=${month}&year=${year}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            document.querySelector('#budgetContent').innerHTML = html;
            document.querySelector('#monthLabel').textContent = `${months[month]} ${year}`;
            document.querySelector('#currentMonthText').textContent = `${months[month]} ${year}`;
            currentMonth = month;
            currentYear = year;
        })
        .catch(err => console.error('Error loading budget:', err));
    }

    // Handle arrow clicks
    document.getElementById('prevMonthBtn').addEventListener('click', () => {
        let newMonth = currentMonth - 1;
        let newYear = currentYear;
        if (newMonth < 1) { newMonth = 12; newYear--; }
        loadBudget(newMonth, newYear);
    });

    document.getElementById('nextMonthBtn').addEventListener('click', () => {
        let newMonth = currentMonth + 1;
        let newYear = currentYear;
        if (newMonth > 12) { newMonth = 1; newYear++; }
        loadBudget(newMonth, newYear);
    });
    
</script>
</html>
