<x-page-header pagetitle="Person" class="bg-secondary"/>

@php
    // Pass $members from the controller; this fallback keeps the view standalone.
    $members = $members ?? [
        ['last' => 'Jornales', 'first' => 'Brian Allan'],
        ['last' => 'Marzan',    'first' => 'Mark Jade'],
    ];
@endphp

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,600;12..96,800&family=Inter:wght@400;500&display=swap">

<style>
    .roster {
        --ink: #171423;
        --paper: #ffffff;
        --wash: #edeaf3;
        --line: #e6e2f0;
        --accent: #4b3fbb;
        --accent-lift: #8b7bee;
        --accent-soft: #e4e0fa;
        --muted: #6b6480;

        background-color: var(--wash);
        font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
        color: var(--ink);
        padding: clamp(40px, 9vw, 88px) 20px;
    }

    .roster__inner {
        max-width: 620px;
        margin: 0 auto;
    }

    .roster__eyebrow {
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 0.72rem;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--accent);
        margin: 0 0 10px;
    }

    .roster__title {
        font-family: 'Bricolage Grotesque', 'Inter', sans-serif;
        font-weight: 800;
        font-size: clamp(2rem, 7vw, 2.9rem);
        line-height: 1.05;
        letter-spacing: -0.025em;
        margin: 0 0 32px;
    }

    /* Panel: one document, hairline-divided rows — not two floating cards. */
    .roster__panel {
        position: relative;
        background-color: var(--paper);
        border: 1px solid var(--line);
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(23, 20, 35, 0.04),
                    0 14px 30px -20px rgba(23, 20, 35, 0.35);
    }

    /* The spine — the original left border, promoted to the whole roster. */
    .roster__panel::before {
        content: '';
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: linear-gradient(to bottom, var(--accent), var(--accent-lift));
    }

    .roster__list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .roster__row {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 18px 24px 18px 28px;
        border-top: 1px solid var(--line);
        transition: background-color 0.18s ease;
    }

    .roster__row:first-child {
        border-top: 0;
    }

    .roster__row:hover {
        background-color: #faf9fe;
    }

    /* Signature: initials instead of a meaningless 01 / 02 sequence. */
    .roster__monogram {
        flex: 0 0 auto;
        width: 52px;
        height: 52px;
        display: grid;
        place-items: center;
        border-radius: 13px;
        background-color: var(--accent-soft);
        box-shadow: inset 0 0 0 1px rgba(75, 63, 187, 0.14);
        color: var(--accent);
        font-family: 'Bricolage Grotesque', sans-serif;
        font-weight: 600;
        font-size: 1.05rem;
        letter-spacing: 0.02em;
    }

    .roster__last {
        display: block;
        font-family: 'Bricolage Grotesque', 'Inter', sans-serif;
        font-weight: 600;
        font-size: 1.2rem;
        letter-spacing: -0.01em;
        line-height: 1.25;
    }

    .roster__first {
        display: block;
        font-size: 0.92rem;
        color: var(--muted);
        margin-top: 2px;
    }

    .roster__role {
        display: block;
        font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
        font-size: 0.7rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--accent);
        margin-top: 6px;
    }

    .roster__empty {
        padding: 28px;
        color: var(--muted);
        text-align: center;
    }

    @media (max-width: 420px) {
        .roster__row {
            gap: 14px;
            padding: 16px 18px 16px 22px;
        }

        .roster__monogram {
            width: 44px;
            height: 44px;
            font-size: 0.92rem;
        }
    }

    /* Quiet entrance, one row after another. */
    @media (prefers-reduced-motion: no-preference) {
        .roster__row {
            opacity: 0;
            animation: roster-in 0.5s ease forwards;
            animation-delay: calc(var(--i) * 90ms);
        }

        @keyframes roster-in {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: none; }
        }
    }
</style>

<section class="roster">
    <div class="roster__inner">

        <header>
            <p class="roster__eyebrow">{{ count($members) }} {{ Str::plural('member', count($members)) }}</p>
            <h1 class="roster__title">Team Members</h1>
        </header>

        <div class="roster__panel">
            <ul class="roster__list">
                @forelse ($members as $i => $member)
                    <li class="roster__row" style="--i: {{ $i }}">
                        <span class="roster__monogram" aria-hidden="true">
                            {{ Str::upper(Str::substr($member['last'], 0, 1) . Str::substr($member['first'], 0, 1)) }}
                        </span>
                        <span>
                            <span class="roster__last">{{ $member['last'] }}</span>
                            <span class="roster__first">{{ $member['first'] }}</span>
                            @isset($member['role'])
                                <span class="roster__role">{{ $member['role'] }}</span>
                            @endisset
                        </span>
                    </li>
                @empty
                    <li class="roster__empty">No members added yet.</li>
                @endforelse
            </ul>
        </div>

    </div>
</section>