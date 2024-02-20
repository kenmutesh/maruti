<?php

namespace App\Models;

use App\Enums\CoatingJobStatusEnum;
use App\Enums\SubscriptionStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\ModelTable;
    // use Traits\Syncer;

    protected $fillable = [
        'name',
        'email',
        'subscription_status',
        'subscription_start_date',
        'subscription_duration',
        'activation_key',
        'key_validity',
        'created_by'
    ];

    protected $casts = [
      'subscription_status' => SubscriptionStatusEnum::class,
    ];

    protected $appends = [
      'subscription_expiry_date',
    ];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function aprotecuser()
    {
      return $this->belongsTo(AprotecUser::class);
    }

    public function userroles()
    {
      return $this->hasMany(Role::class);
    }

    public function locations()
    {
      return $this->hasMany(Location::class);
    }

    public function warehouses()
    {
      return $this->hasMany(Warehouse::class);
    }

    public function floors()
    {
      return $this->hasMany(Floor::class);
    }

    public function shelves()
    {
      return $this->hasMany(Shelf::class);
    }

    public function bins()
    {
      return $this->hasMany(Bin::class);
    }

    public function users()
    {
      return $this->hasMany(User::class);
    }

    public function suppliers()
    {
      return $this->hasMany(Supplier::class);
    }

    public function customers()
    {
      return $this->hasMany(Customer::class);
    }

    public function coatingjobs()
    {
      return $this->hasMany(CoatingJob::class);
    }

    public function opencoatingjobs()
    {
      $instance = $this->hasMany(CoatingJob::class);
      $instance->getQuery()->where('status', CoatingJobStatusEnum::OPEN);
      return $instance;
    }

    public function closedcoatingjobs()
    {
      $instance = $this->hasMany(CoatingJob::class);
      $instance->getQuery()->where('status', CoatingJobStatusEnum::CLOSED);
      return $instance;
    }

    public function cancelledcoatingjobs()
    {
      return $this->hasMany(CoatingJob::class);
    }

    public function inventoryitems() {
      return $this->hasMany(InventoryItem::class);
    }

    public function noninventoryitems() {
      return $this->hasMany(NonInventoryItem::class);
    }

    public function powders() {
      return $this->hasMany(Powder::class);
    }

    public function purchaseorders() {
      return $this->hasMany(PurchaseOrder::class);
    }

    public function invoices() {
      return $this->hasMany(Invoice::class);
    }

    public function cashsales() {
      return $this->hasMany(CashSale::class);
    }

    public function payments() {
      return $this->hasMany(Payment::class);
    }

    public function customercreditnotes() {
      return $this->hasMany(CustomerCreditNote::class);
    }

    public function getSubscriptionExpiryDateAttribute()
    {
      if($this->subscription_start_date == null || $this->subscription_start_date == ''){
        return null;
      }
        $dateCreated = new Carbon($this->subscription_start_date);
        $dateCreated->addDays($this->subscription_duration);
        return $dateCreated;
    }
}
