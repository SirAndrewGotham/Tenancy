<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, BelongsToTenant;

    protected static function boot(): void
    {
        parent::boot();

        self::creating(function ($model) {
            $slug = Str::slug($model->name);
            $originalSlug = $slug;
            $count = 2;
            while (static::whereSlug($slug)->exists()) {
                $slug = $originalSlug . '-' . $count++;
            }
            $model->slug = $slug;
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'role',
        'email',
        'password',
        'tenant_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }





    public function avatarUrl()
    {
        if($this->photo) {
//            return Storage::disk('s3-public')->url($this->photo);
            return Storage::disk('user')->url($this->photo);
        }
        return 'https://api.dicebear.com/9.x/initials/svg?seed=' . $this->name;
    }

    public static function search($query)
    {
        return empty($query) ? static::query()
            : static::where('name', 'like', '%'.$query.'%')
                ->orWhere('email', 'like', '%'.$query.'%');
    }

    public function isAdmin()
    {
        return $this->role == 'Admin';
    }

    public function isHR()
    {
        return $this->role == 'Human Resources';
    }

    public function applicationUrl()
    {
        if($this->application()) {
            return url('/documents/' . $this->id . '/' . $this->application()->filename);
        }
        return '#';
    }

    public function application()
    {
        return $this->documents()->where('type', 'application')->first();
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }
}
