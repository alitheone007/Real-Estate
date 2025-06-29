import { type ClassValue, clsx } from "clsx"
import { twMerge } from "tailwind-merge"
 
export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
} 

export async function sendAnalyticsEvent(data: any) {
  try {
    const res = await fetch('/api/analytics/track.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
    return await res.json();
  } catch (e) {
    return { success: false, error: e };
  }
}

export async function sendUserActivity(data: any) {
  try {
    const res = await fetch('/api/analytics/activity.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data),
    });
    return await res.json();
  } catch (e) {
    return { success: false, error: e };
  }
} 