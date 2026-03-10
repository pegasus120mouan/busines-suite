<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'domain' => $this->domain,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'logo' => $this->logo,
            'currency' => $this->currency,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
            'settings' => $this->settings,
            'status' => $this->status,
            'plan' => $this->plan,
            'trial_ends_at' => $this->trial_ends_at,
            'subscription_ends_at' => $this->subscription_ends_at,
            'is_on_trial' => $this->isOnTrial(),
            'has_active_subscription' => $this->hasActiveSubscription(),
            'users_count' => $this->whenCounted('users'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
