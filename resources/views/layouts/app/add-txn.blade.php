<!DOCTYPE html>
<html lang="en">
@php
    $page = 'transactions';
    $common_files = 'main';
@endphp
@include('include.header', ['page' => $page], ['common_files' => $common_files])
<body>
    <section class="page-container">
        <div class="page-header">
            <i class="fi fi-rr-angle-small-left"></i>
            <div class="header-title">
                <p>Add New Transcation</p>
            </div>
        </div>
        <div class="txn-page-content">
            <div class="toggle-btns">
                <p>Expense</p>
                <p>Income</p>
            </div>
            <div class="txn-amt-input">
                <input type="text" name="txn_amt" id="txn_amt" />
            </div>
            <div class="txn-accounts">
                <div class="account-title">
                    <p>Select Account</p>
                </div>
                <div class="account-container">
                    <div class="acc-container">
                        <div class="logo-container">
                            <img src="../../assets/images/money.png" />
                            <i class="fi fi-sc-check-circle"></i>
                        </div>
                        <div class="account-name">
                            <p>HDFC Bank</p>
                        </div>
                        <div class="account-bal">
                            <p><i class="fi fi-sc-indian-rupee-sign"></i> 500</p>
                        </div>
                    </div>
                    <div class="acc-container">
                        <div class="logo-container">
                            <img src="../../assets/images/money.png" />
                            <i class="fi fi-sc-check-circle"></i>
                        </div>
                        <div class="account-name">
                            <p>HDFC Bank</p>
                        </div>
                        <div class="account-bal">
                            <p><i class="fi fi-sc-indian-rupee-sign"></i> 500</p>
                        </div>
                    </div>
                </div>
                <div class="category-container">
                    <div class="category-title">
                        <p>Select Category</p>
                    </div>
                    <div class="category-card">
                        <div class="category-content">
                            <img src="../../assets/images/liquor.png" />
                            <p>Alcohol</p>
                        </div>
                        <div class="category-content">
                            <img src="../../assets/images/liquor.png" />
                            <p>Alcohol</p>
                        </div>
                        <div class="category-content">
                            <img src="../../assets/images/liquor.png" />
                            <p>Alcohol</p>
                        </div>
                    </div>
                </div>
                <div class="description-container">
                    <div class="notes-title">
                        <p style="margin: 6px 0 9px!important;">Notes (Optional)</p>
                    </div>
                    <textarea name="description" rows="2" cols="50" placeholder="Notes"></textarea>
                </div>
                <div class="date-time-container">
                    <div class="date-title">
                        <p style="margin-top:12px!important;">Pick Date</p>
                    </div>
                    <input type="datetime-local" id="datetime-picker"  />
                </div>
            </div>
            <div class="add-txn-btn">
                <button>Add Transaction</button>
            </div>
        </div>
    </section>
</body>
<script>
    const now = new Date();

    // Format the date and time as yyyy-MM-ddTHH:mm
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0'); // months are 0-based, so add 1
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');

    const formattedDate = `${year}-${month}-${day}T${hours}:${minutes}`;

    // Set the value of the datetime-local input
    document.getElementById('datetime-picker').value = formattedDate;
</script>
</html>