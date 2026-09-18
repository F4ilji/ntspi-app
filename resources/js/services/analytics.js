import { router } from '@inertiajs/vue3'

const CONSENT_KEY = 'analytics_consent'
const VISITOR_KEY = '_vid'

function getVisitorId() {
    let vid = localStorage.getItem(VISITOR_KEY)
    if (!vid) {
        vid = crypto.randomUUID()
        localStorage.setItem(VISITOR_KEY, vid)
    }
    return vid
}

function isExcludedPath() {
    const path = window.location.pathname
    return path.startsWith('/dashboard') || path.startsWith('/admin')
}

function sendPayload(payload) {
    const blob = new Blob([JSON.stringify(payload)], { type: 'application/json' })

    if (navigator.sendBeacon) {
        const sent = navigator.sendBeacon('/api/track/hit', blob)
        if (sent) return
    }

    fetch('/api/track/hit', {
        method: 'POST',
        body: JSON.stringify(payload),
        headers: { 'Content-Type': 'application/json' },
        keepalive: true,
    }).catch(() => {})
}

export function getConsent() {
    return localStorage.getItem(CONSENT_KEY)
}

export function trackCurrentPage() {
    if (typeof window === 'undefined') return
    if (getConsent() !== 'granted') return
    if (isExcludedPath()) return

    sendPayload({
        visitor_id: getVisitorId(),
        url: window.location.pathname + window.location.search,
        referrer: document.referrer || null,
        screen_resolution: `${window.screen.width}x${window.screen.height}`,
        title: document.title,
        timestamp: Date.now(),
    })
}

let listenerRegistered = false

export function initAnalytics() {
    if (typeof window === 'undefined') return

    if (!listenerRegistered) {
        listenerRegistered = true
        router.on('navigate', () => {
            setTimeout(trackCurrentPage, 100)
        })
    }

    window._ntspiTrackHit = trackCurrentPage
}
