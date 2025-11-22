@php
    $payload = $budgetData ?? [];
    $budgets = data_get($payload, 'data.budgets', []);

    $totalBudget = (float) ($payload['data']['total_budget'] ?? 0);
    $totalSpent = (float) ($payload['data']['total_spent'] ?? 0);
    $totalRemaining = (float) ($payload['data']['total_remaining'] ?? 0);

    // Avoid division by zero
    $overallProgress = $totalBudget > 0 ? ($totalSpent / $totalBudget) * 100 : 0;

    $overallProgress = min(max($overallProgress, 0), 100);

    $overallProgressClass = $overallProgress < 70 
        ? 'bg-success-custom' 
        : ($overallProgress < 90 ? 'bg-warning-custom' : 'bg-danger-custom');

    $chartLabels = [];
    $chartValues = [];
    $chartColors = [];

    foreach ($budgets as $b) {
        $chartLabels[] = $b['category'];
        $chartValues[] = round($b['progress'], 2);  // percentage
        $chartColors[] = $b['category_color'];
    }
@endphp

@if (count($budgets) > 0)
    <!-- <div class="month-wise-overallBudget-details">
        <div class="total-budget text-center">
            <p class="fw-bold"><img src="../../assets/images/budget/wallet.png"/>Total Budget</p>
            <p class="ruppee-class"><i class="fi fi-rr-indian-rupee-sign"></i>{{ $totalBudget }}</p>
        </div>

        <div class="total-spent text-center">
            <p class="fw-bold"><img src="../../assets/images/budget/expenses.png"/>Total Spent</p>
            <p class="ruppee-class red"><i class="fi fi-rr-indian-rupee-sign"></i>{{ $totalSpent }}</p>
        </div>

        <div class="total-remaining text-center">
            <p class="fw-bold"><img src="../../assets/images/budget/scale.png"/>Remaining</p>
            <p class="ruppee-class yellow"><i class="fi fi-rr-indian-rupee-sign"></i>{{ $totalRemaining }}</p>
        </div>
    </div> -->
    
    <div class="overall-budget-container">
        <h1>Monthly Budget Limit</h1>
        <div class="overall-limit-details">
            <p class="o-total-budget"><i class="fi fi-sc-indian-rupee-sign"></i>{{ $totalBudget }}</p>
            
            <div class="overall-progress-wrapper" style="margin: 15px 0;">
                <!-- <p class="text-center fw-bold" style="margin-bottom: 5px;">Overall Usage: {{ round($overallProgress, 1) }}%</p> -->
                 <div class="progress-budget-conatiner">
                    <p><i class="fi fi-sc-indian-rupee-sign"></i>{{ $totalSpent }} / <span class="limit"><i class="fi fi-sc-indian-rupee-sign"></i>{{ $totalBudget }}</span></p>
                </div>
                <div class="progress-wrapper">
                    <div class="progress-bar-custom {{ $overallProgressClass }}" 
                        style="width: {{ $overallProgress }}%;">
                    </div>
                </div>
                <p class="budget-message">{{ $payload['data']['overall_message'] ?? '' }}</p>
            </div>
        </div>
    </div> 

    <div class="chart-budget-container">
        <div class="chart-spent-details">
            <p>You have spent <span><i class="fi fi-sc-indian-rupee-sign" style="font-size: 8px;"></i>{{ $totalSpent }}</span> this month</p>
            <i class="fi fi-tc-menu-dots" style="margin-top: 2px;"></i>
        </div>
        
        <div id="gauge-container" style="width:200px;margin:auto;"></div>
        <div id="gauge-legend"></div>
    </div>


    <div class="row">
        @foreach ($budgets as $item)
            @php
                $category = $item['category'] ?? 'Uncategorized';
                $icon = $item['category_icon'] ?? 'fi fi-rr-wallet';
                $limit = (float) ($item['budget_limit'] ?? 0);
                $spent = (float) ($item['spent'] ?? 0);
                $remaining = $item['remaining'] ?? ($limit - $spent);
                $progress = $item['progress'] ?? ($limit > 0 ? ($spent / $limit) * 100 : 0);
                $progress = min(max($progress, 0), 100);
                $progressClass = $progress < 70 ? 'bg-success-custom' : ($progress < 90 ? 'bg-warning-custom' : 'bg-danger-custom');
            @endphp

            <div class="col-md-4 mb-3">
                <div class="budget-card">
                    <div class="budget-sec1-container">
                        <h6 class="fw-bold mb-2">
                            <img src="{{ asset('storage/' . $icon) }}" width="50">
                            {{ $category }}
                        </h6>
                        <i class="fi fi-tc-menu-dots"></i>
                    </div>
                    <div class="budget-details">
                        <p><i class="fi fi-sc-indian-rupee-sign"></i>{{ number_format($remaining, 2) }}</p>
                    </div>
                    <div class="progress-budget-conatiner">
                        <p><i class="fi fi-sc-indian-rupee-sign"></i>{{ number_format($spent, 2) }} / <span class="limit"><i class="fi fi-sc-indian-rupee-sign"></i>{{ number_format($limit, 2) }}</span></p>
                    </div>
                    <div class="progress-wrapper">
                        <div class="progress-bar-custom {{ $progressClass }}" style="width: {{ $progress }}%;"></div>
                    </div>
                    <!-- <p class="budget-used">{{ round($progress, 1) }}% used</p> -->
                    <p class="budget-message">{{ $item['message'] ?? '' }}</p>
                   
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="no-data">No budget data available for this month.</p>
@endif

<script>
    const labels = @json($chartLabels);
    const values = @json($chartValues);
    const colors = @json($chartColors);

    // Build categories array
    const categories = labels.map((label, index) => ({
        name: label,
        value: values[index],
        color: colors[index]
    }));

    // ---------- CHART DRAWING ----------
    function drawGauge(containerId, categories) {
        const strokeWidth = 22;
        const radius = 60;

        // CENTER of arc (bigger viewbox prevents arc cutting)
        const cx = 100, cy = 75;

        const total = categories.reduce((s, c) => s + c.value, 0);
        let start = 180;

        let svg = `
            <svg viewBox="0 0 200 140" width="100%" height="100%">
                <path 
                    d="${arcPath(cx, cy, radius, 180, 360)}"
                    stroke="#2e2e2e"
                    stroke-width="${strokeWidth}"
                    fill="none"
                    stroke-linecap="butt"
                />
        `;

        categories.forEach(cat => {
            const percent = cat.value / total;
            const sweep = 180 * percent;
            const end = start + sweep;

            svg += `
                <path 
                    d="${arcPath(cx, cy, radius, start, end)}"
                    stroke="${cat.color}"
                    stroke-width="${strokeWidth}"
                    fill="none"
                    stroke-linecap="butt"
                />
            `;

            start = end;
        });

        svg += "</svg>";

        document.getElementById(containerId).innerHTML = svg;
    }

    // Build arc path
    function arcPath(cx, cy, r, startAngle, endAngle) {
        const start = polar(cx, cy, r, startAngle);
        const end = polar(cx, cy, r, endAngle);
        const largeArc = endAngle - startAngle > 180 ? 1 : 0;

        return `M${start.x},${start.y} A${r},${r} 0 ${largeArc} 1 ${end.x},${end.y}`;
    }

    function polar(cx, cy, r, angle){
        const rad = angle * Math.PI / 180;
        return {
            x: cx + r * Math.cos(rad),
            y: cy + r * Math.sin(rad)
        };
    }

    // ---------- LEGEND ----------
    function renderLegend(containerId, categories) {
        let html = "";

        categories.forEach(cat => {
            html += `
                <div class="legend-row">
                    <span class="legend-color" style="background:${cat.color}"></span>
                    <span class="legend-label">${cat.name}</span>
                    <span class="legend-value">${cat.value}%</span>
                </div>
            `;
        });

        document.getElementById(containerId).innerHTML = html;
    }

    // ---------- RUN ----------
    drawGauge("gauge-container", categories);
    renderLegend("gauge-legend", categories);
</script>