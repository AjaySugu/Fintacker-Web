<style>

    /* =======================
       GLOBAL STYLES
    ======================= */
    .sub-pro-container {
        padding-bottom: 40px;
    }

    .sub-pro-section-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 12px;
        margin-top: 10px;
        color: #222;
    }

    /* =======================
       SUMMARY CARDS
    ======================= */

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        margin-bottom: 20px;
    }

    .summary-card {
        background: #ffffff;
        padding: 18px 16px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .summary-title {
        color: #555;
        font-size: 14px;
        margin-bottom: 6px;
    }

    .summary-value {
        font-size: 22px;
        font-weight: 800;
        color: #1a73e8;
    }

    /* =======================
       FILTER TABS
    ======================= */

    .tab-list {
        display: flex;
        justify-content: space-between;
        background: #ffffff;
        width: 74%;
        padding: 7px;
        margin: 0px auto 24px;
        border-radius: 50px;
        box-shadow: rgba(0, 0, 0, 0.1) 0px 2px 6px;
    }

    .tab-btn {
        padding: 8px 18px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: 0.2s;
        width: 100%;
        text-align: center;
    }

    .tab-btn.active {
        background: #ffffff;
        color: #000000;
        box-shadow: inset rgba(0, 0, 0, 0.1) 0px 4px 12px;
    }

    /* =======================
       SEARCH BAR
    ======================= */

    .search-bar {
        margin-bottom: 22px;
        position: relative;
    }

    .search-bar input {
        width: 70%;
        padding: 10px 12px;
        border-radius: 10px;
        border: 2px solid #f5f5f5;
        font-size: 13px;
        margin: auto;
        display: flex;
        font-family: inherit;
    }

    /* =======================
       SUBSCRIPTION CARD
    ======================= */

    .subscription-pro-card {
        background: #ffffff;
        border-radius: 16px;
        /* padding: 18px 20px; */
        margin-bottom: 20px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        transition: 0.25s ease;
        display: flex;
        gap: 14px;
        position: relative;
        flex-direction: column;
    }
    .subscription-pro-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.12);
    }

    /* Icon box */
    .sub-icon-box {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        background: #f1f4ff;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 26px;
        color: #1a73e8;
    }

    .sub-body {
        flex-grow: 1;
    }

    .sub-body-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Date + Status */
    .sub-pro-info-row {
        margin-top: 14px;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .sub-pro-date-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 3px solid #1a73e8;
        display: flex;
        justify-content: center;
        align-items: center;
        font-weight: 800;
        color: #1a73e8;
        font-size: 14px;
        background: #f5f9ff;
    }

    .sub-pro-status {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    /* Status Colors */
    .status-today { color: #ff9800; }
    .status-tomorrow { color: #2196f3; }
    .status-overdue { color: #d81b60; }
    .status-upcoming { color: #4caf50; }

    /* Reminder */
    .sub-pro-reminder {
        margin-top: -4px;
        background: #fdfcfa;
        border-left: 4px solid #f1efe9;
        padding: 10px 12px;
        font-size: 12px;
        color: #6d5d00;
        text-align: justify;
        margin-bottom: -15px;
    }

</style>


<div class="sub-pro-container">

    <!-- =======================
         SUMMARY CARDS
    ======================= -->

    @php
        $totalAmount = array_sum(array_column($Subscriptions, 'amount'));
        $upcoming = collect($Subscriptions)->filter(fn($s) => $s['next_payment'] > date('Y-m-d'))->count();
        $overdue = collect($Subscriptions)->filter(fn($s) => $s['next_payment'] < date('Y-m-d'))->count();
    @endphp

    <!-- <div class="summary-grid">
        <div class="summary-card">
            <p class="summary-title">Total Subscriptions</p>
            <p class="summary-value">{{ count($Subscriptions) }}</p>
        </div>
        <div class="summary-card">
            <p class="summary-title">Monthly Total</p>
            <p class="summary-value">₹{{ number_format($totalAmount,2) }}</p>
        </div>
        <div class="summary-card">
            <p class="summary-title">Upcoming</p>
            <p class="summary-value">{{ $upcoming }}</p>
        </div>
        <div class="summary-card">
            <p class="summary-title">Overdue</p>
            <p class="summary-value">{{ $overdue }}</p>
        </div>
    </div> -->

    <div class="summary-container">
        <div class="subs-details">
            <p> 
                <img src="../../assets/images/subscription.png" />
                <span>Active Subscriptions</span>
            </p>
            <hr>
            <p class="summary-value">{{ count($Subscriptions) }}</p>
        </div>
        <div class="subs-details">
            <p> 
                <img src="../../assets/images/time-limit.png" />
                <span>Overdue for the month</span>
            </p>
            <hr>
            <p class="summary-value">{{ $overdue }}</p>
        </div>
        <div class="subs-details">
            <p> 
                <img src="../../assets/images/rupee.png" />
                <span>Monthly Total</span>
            </p>
            <hr>
            <p class="summary-value">₹{{ number_format($totalAmount,2) }}</p>
        </div>
    </div>


    <!-- =======================
         FILTER TABS
    ======================= -->

    <div class="tab-list">
        <div class="tab-btn active" data-filter="all">All</div>
        <div class="tab-btn" data-filter="upcoming">Upcoming</div>
        <div class="tab-btn" data-filter="overdue">Overdue</div>
        <!-- <div class="tab-btn" data-filter="today">Today</div>
        <div class="tab-btn" data-filter="tomorrow">Tomorrow</div> -->  
    </div>


    <!-- =======================
         SEARCH BAR
    ======================= -->

    <div class="search-bar">
        <input type="text" id="searchInput" placeholder="Search subscriptions...">
    </div>


    <!-- =======================
         SUBSCRIPTION CARDS
    ======================= -->

    @php
        function detectStatus($date) {
            $today = date('Y-m-d');
            $tomorrow = date('Y-m-d', strtotime('+1 day'));

            if ($date == $today) return 'today';
            if ($date == $tomorrow) return 'tomorrow';
            if ($date < $today) return 'overdue';
            return 'upcoming';
        }

        function categoryIcon($cat) {
            return match(strtolower($cat)) {
                "entertainment" => "🎬",
                "utilities" => "💡",
                "subscriptions" => "📦",
                "mutual funds" => "📈",
                "finance", "loan" => "💰",
                default => "📌"
            };
        }
    @endphp
    
    <div id="subscriptionList">
        @foreach($Subscriptions as $sub)

            @php
                $status = detectStatus($sub['next_payment']);
                $icon = categoryIcon($sub['category']);
                $c_icon = $sub['category_icon'];
                $day = date('d', strtotime($sub['next_payment']));
                $month = date('M', strtotime($sub['next_payment']));
            @endphp

            <div class="subscription-pro-card sub-item" data-status="{{ $status }}" data-title="{{ strtolower($sub['name']) }}">
                <div class="subs-header">
                    <div style="display: flex;">
                        <img src="{{ asset('storage/' . $c_icon) }}" />
                        <div class="subs-header-name">
                            <div class="sub-pro-category">{{ $sub['category'] }}</div>
                            <p class="sub-pro-title">{{ $sub['name'] }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="sub-account">HDFC Account</p>
                        <p class="sub-pro-amount">₹{{ $sub['amount'] }}</p>
                    </div>
                </div>
                @if($sub['reminder_message'])
                    <div class="sub-pro-reminder">
                        {{ $sub['reminder_message'] }}
                    </div>
                @endif
                <div class="subscription-footer">
                    <p class="subs-type">Autopay</p>
                    <p class="subs-payment-date payment-status-tag {{ $status }}">
                        @if($status == 'today')
                            Today — {{ $month }} {{ $day }}
                        @elseif($status == 'tomorrow')
                            Tomorrow — {{ $month }} {{ $day }}
                        @elseif($status == 'overdue')
                            Overdue — {{ $month }} {{ $day }}
                        @else
                            Next Payment — {{ $month }} {{ $day }}
                        @endif
                    </p>
                </div>
                
                
                <!-- <div class="sub-body">
                    <div class="sub-body-header">
                        
                       
                    </div>

                    

                    <div class="sub-pro-info-row">
                        <div class="sub-pro-date-circle">{{ $day }}</div>
                        <div>
                            <p class="sub-pro-status status-{{ $status }}">{{ ucfirst($status) }}</p>
                            <p style="color:#555;font-size:14px;">Next Payment — {{ $month }} {{ $day }}</p>
                        </div>
                    </div>

                    @if($sub['reminder_message'])
                    <div class="sub-pro-reminder">
                        {{ $sub['reminder_message'] }}
                    </div>
                    @endif
                </div> -->
            </div>

        @endforeach
    </div>

</div>



<!-- =======================
     JS FOR FILTER + SEARCH
======================= -->
<script>
document.querySelectorAll('.tab-btn').forEach(tab => {
    tab.addEventListener('click', function() {

        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');

        let filter = this.getAttribute('data-filter');
        let items = document.querySelectorAll('.sub-item');

        items.forEach(item => {
            let status = item.getAttribute('data-status');

            if (filter === 'all' || filter === status) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });

    });
});

// SEARCH FUNCTION
document.getElementById('searchInput').addEventListener('keyup', function() {
    let term = this.value.toLowerCase();
    let items = document.querySelectorAll('.sub-item');

    items.forEach(item => {
        let title = item.getAttribute('data-title');

        if (title.includes(term)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
