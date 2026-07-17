// Reload page
function reload() {
    window.location.reload(); 
} 

// Redirect to given url
function redirect(url) {
    window.location.href = url; 
} 

// Save redirect if url exists
async function redirectIfExists(url, fallback = "/", use_fallback = true) {
    try {
        const response = await fetch(url, {
            method: "HEAD" 
        }); 
        if (response.ok) {
            redirect(url); 
            return; 
        } 
    } catch (error) {
        console.warn(url + " doesn't exist.");
    }

    if (use_fallback) {
        redirect(fallback); 
    }
    return false; 
} 

// Redirect to last page or given fallback
function backOrFallback(fallback = "/") {
    if (document.referrer && document.referrer.startsWith(window.location.origin)) {
        window.history.back(); 
        return; 
    } 
    redirect(fallback); 
} 