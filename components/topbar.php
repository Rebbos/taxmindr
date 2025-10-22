<!-- Decorative Top Bar - Accent Line (at the very top of page) -->
<div class="topbar-accent"></div>

<style>
.topbar-accent {
    height: 4px;
    width: 100%;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 9999;
    background: linear-gradient(90deg, 
        var(--tm-primary), 
        var(--tm-secondary), 
        #8b5cf6,
        var(--tm-secondary),
        var(--tm-primary));
    background-size: 200% 100%;
    animation: gradientShift 8s ease infinite;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.2);
}

/* Animated gradient effect */
@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Add padding to body to account for topbar */
body {
    padding-top: 4px;
}
</style>
