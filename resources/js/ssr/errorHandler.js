/**
 * SSR Error Handler
 * Captures and logs SSR-specific errors to a separate log file
 */

import fs from 'fs';
import path from 'path';

const SSR_LOG_FILE = path.join(process.cwd(), 'storage', 'logs', 'ssr-errors.log');

/**
 * Format error for logging
 */
function formatError(error) {
    const timestamp = new Date().toISOString();
    const errorDetails = {
        timestamp,
        message: error.message || 'Unknown error',
        stack: error.stack || 'No stack trace',
        type: error.constructor.name,
    };

    return JSON.stringify(errorDetails) + '\n';
}

/**
 * Log error to separate SSR error file
 */
export function logSsrError(error) {
    try {
        // Ensure directory exists
        const logDir = path.dirname(SSR_LOG_FILE);
        if (!fs.existsSync(logDir)) {
            fs.mkdirSync(logDir, { recursive: true });
        }

        // Append error to log file
        fs.appendFileSync(SSR_LOG_FILE, formatError(error), 'utf8');
    } catch (logError) {
        // Fallback to console if file write fails
        console.error('Failed to write SSR error log:', logError);
        console.error('Original SSR error:', error);
    }
}

/**
 * Create SSR error handler middleware
 * Usage: Wrap your SSR render function with this
 */
export function createSsrErrorHandler() {
    return (error) => {
        logSsrError(error);
        
        // Return true to suppress error propagation
        // Return false to also throw the error
        return true;
    };
}
