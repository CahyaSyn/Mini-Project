<div id="sidebar-wrapper">
    <ul class="sidebar-nav">
        <li class="sidebar-brand">
            <a href="#">AdminPanel</a>
        </li>
        {{-- <li><a href="{{ route('pages.dashboard') }}" class="nav-link" data-url="{{ route('pages.dashboard') }}"><i class="fa-solid fa-gauge"></i> Dashboard</a></li> --}}
        <li><a href="{{ route('pages.accounts') }}" class="nav-link" data-url="{{ route('pages.accounts') }}"><i class="fa-solid fa-chart-simple"></i> Chart Of Account</a></li>
        <li><a href="{{ route('pages.journals') }}" class="nav-link" data-url="{{ route('pages.journals') }}"><i class="fa-solid fa-book"></i> Journals</a></li>
        <li><a href="{{ route('pages.invoices') }}" class="nav-link" data-url="{{ route('pages.invoices') }}"><i class="fa-solid fa-file-invoice"></i> Invoices</a></li>
        <li><a href="{{ route('pages.payments') }}" class="nav-link" data-url="{{ route('pages.payments') }}"><i class="fa-solid fa-money-bill"></i> Payments</a></li>
        <li><a href="{{ route('pages.trial_balance') }}" class="nav-link" data-url="{{ route('pages.trial_balance') }}"><i class="fa-solid fa-scale-balanced"></i> Trial Balance</a></li>
    </ul>
</div>
