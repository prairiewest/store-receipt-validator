<?php

namespace ReceiptValidator\AppleAppStore;

use ArrayAccess;
use Carbon\Carbon;
use ReceiptValidator\AbstractTransaction;
use ReceiptValidator\Environment;
use ReceiptValidator\Exceptions\ValidationException;
use ReturnTypeWillChange;

/**
 * Represents a transaction in the Apple App Store Server API.
 * @implements ArrayAccess<string, mixed>
 */
class Transaction extends AbstractTransaction implements ArrayAccess
{
    /** @var array<string, mixed>|null */
    protected ?array $rawData;

    /** @var string|null The original transaction identifier of a purchase. */
    protected ?string $originalTransactionId = null;

    /** @var string|null The unique identifier of subscription-purchase events across devices. */
    protected ?string $webOrderLineItemId = null;

    /** @var string|null The bundle identifier of an app. */
    protected ?string $bundleId = null;

    /** @var string|null The identifier of the subscription group. */
    protected ?string $subscriptionGroupIdentifier = null;

    /** @var Carbon|null The time that the App Store charged the user's account. */
    protected ?Carbon $purchaseDate = null;

    /** @var Carbon|null The purchase date of the transaction associated with the original transaction identifier. */
    protected ?Carbon $originalPurchaseDate = null;

    /** @var Carbon|null The expiration date of an auto-renewable subscription. */
    protected ?Carbon $expiresDate = null;

    /** @var string|null The type of the in-app purchase. */
    protected ?string $type = null;

    /** @var string|null UUID to map a customer's in-app purchase with its App Store transaction. */
    protected ?string $appAccountToken = null;

    /** @var string|null Describes whether the transaction was purchased or shared via Family Sharing. */
    protected ?string $inAppOwnershipType = null;

    /** @var Carbon|null The time that the App Store signed the JWS data. */
    protected ?Carbon $signedDate = null;

    /** @var string|null The reason the App Store refunded or revoked the transaction. */
    protected ?string $revocationReason = null;

    /** @var Carbon|null The time that Apple Support refunded the transaction. */
    protected ?Carbon $revocationDate = null;

    /** @var bool|null Indicates whether the user upgraded to another subscription. */
    protected ?bool $isUpgraded = null;

    /** @var string|null Represents the promotional offer type. */
    protected ?string $offerType = null;

    /** @var string|null Identifier for the promo code or promotional offer. */
    protected ?string $offerIdentifier = null;

    /** @var string|null Three-letter code for the App Store storefront country/region. */
    protected ?string $storefront = null;

    /** @var string|null Value that identifies the App Store storefront. */
    protected ?string $storefrontId = null;

    /** @var string|null Indicates whether it's a customer purchase or a renewal. */
    protected ?string $transactionReason = null;

    /** @var string|null ISO 4217 currency code. */
    protected ?string $currency = null;

    /** @var int|null Price in milliunits. */
    protected ?int $price = null;

    /** @var string|null Payment mode for an offer. */
    protected ?string $offerDiscountType = null;

    /** @var string|null The app transaction identifier. */
    protected ?string $appTransactionId = null;

    /** @var string|null The duration of the offer. */
    protected ?string $offerPeriod = null;

    /**
     * Environment in which validation was performed.
     *
     * @var Environment
     */
    protected Environment $environment;

    /**
     * @return array<string, mixed>|null
     */
    public function getRawData(): ?array
    {
        return $this->rawData;
    }

    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        $this->rawData[$offset] = $value;
        $this->parse();
    }

    public function parse(): self
    {
        if (!is_array($this->rawData)) {
            throw new ValidationException('Response must be an array');
        }

        $data = $this->rawData;

        $this->originalTransactionId = $data['originalTransactionId'] ?? null;
        $this->transactionId = $data['transactionId'] ?? null;
        $this->webOrderLineItemId = $data['webOrderLineItemId'] ?? null;
        $this->bundleId = $data['bundleId'] ?? null;
        $this->productId = $data['productId'] ?? null;
        $this->subscriptionGroupIdentifier = $data['subscriptionGroupIdentifier'] ?? null;
        $this->quantity = $data['quantity'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->appAccountToken = $data['appAccountToken'] ?? null;
        $this->inAppOwnershipType = $data['inAppOwnershipType'] ?? null;
        $this->revocationReason = $data['revocationReason'] ?? null;
        $this->isUpgraded = $data['isUpgraded'] ?? null;
        $this->offerType = $data['offerType'] ?? null;
        $this->offerIdentifier = $data['offerIdentifier'] ?? null;
        $this->storefront = $data['storefront'] ?? null;
        $this->storefrontId = $data['storefrontId'] ?? null;
        $this->transactionReason = $data['transactionReason'] ?? null;
        $this->currency = $data['currency'] ?? null;
        $this->price = $data['price'] ?? null;
        $this->offerDiscountType = $data['offerDiscountType'] ?? null;
        $this->appTransactionId = $data['appTransactionId'] ?? null;
        $this->offerPeriod = $data['offerPeriod'] ?? null;

        $this->environment = $data['environment'] === 'Production'
            ? Environment::PRODUCTION
            : Environment::SANDBOX;

        if (!empty($data['purchaseDate'])) {
            $this->purchaseDate = Carbon::createFromTimestampMs($data['purchaseDate']);
        }

        if (!empty($data['originalPurchaseDate'])) {
            $this->originalPurchaseDate = Carbon::createFromTimestampMs($data['originalPurchaseDate']);
        }

        if (!empty($data['expiresDate'])) {
            $this->expiresDate = Carbon::createFromTimestampMs($data['expiresDate']);
        }

        if (!empty($data['signedDate'])) {
            $this->signedDate = Carbon::createFromTimestampMs($data['signedDate']);
        }

        if (!empty($data['revocationDate'])) {
            $this->revocationDate = Carbon::createFromTimestampMs($data['revocationDate']);
        }

        return $this;
    }

    #[ReturnTypeWillChange]
    public function offsetGet($offset): mixed
    {
        return $this->rawData[$offset] ?? null;
    }

    #[ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        unset($this->rawData[$offset]);
    }

    #[ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        return isset($this->rawData[$offset]);
    }

    /**
     * Get the environment used.
     *
     * @return Environment
     */
    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    /**
     * Set the environment.
     *
     * @param Environment $environment
     * @return $this
     */
    public function setEnvironment(Environment $environment): self
    {
        $this->environment = $environment;
        return $this;
    }

    public function getOriginalTransactionId(): ?string
    {
        return $this->originalTransactionId;
    }

    public function getWebOrderLineItemId(): ?string
    {
        return $this->webOrderLineItemId;
    }

    public function getBundleId(): ?string
    {
        return $this->bundleId;
    }

    public function getSubscriptionGroupIdentifier(): ?string
    {
        return $this->subscriptionGroupIdentifier;
    }

    public function getPurchaseDate(): ?Carbon
    {
        return $this->purchaseDate;
    }

    public function getOriginalPurchaseDate(): ?Carbon
    {
        return $this->originalPurchaseDate;
    }

    public function getExpiresDate(): ?Carbon
    {
        return $this->expiresDate;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getAppAccountToken(): ?string
    {
        return $this->appAccountToken;
    }

    public function getInAppOwnershipType(): ?string
    {
        return $this->inAppOwnershipType;
    }

    public function getSignedDate(): ?Carbon
    {
        return $this->signedDate;
    }

    public function getRevocationReason(): ?string
    {
        return $this->revocationReason;
    }

    public function getRevocationDate(): ?Carbon
    {
        return $this->revocationDate;
    }

    public function getIsUpgraded(): ?bool
    {
        return $this->isUpgraded;
    }

    public function getOfferType(): ?string
    {
        return $this->offerType;
    }

    public function getOfferIdentifier(): ?string
    {
        return $this->offerIdentifier;
    }

    public function getStorefront(): ?string
    {
        return $this->storefront;
    }

    public function getStorefrontId(): ?string
    {
        return $this->storefrontId;
    }

    public function getTransactionReason(): ?string
    {
        return $this->transactionReason;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getOfferDiscountType(): ?string
    {
        return $this->offerDiscountType;
    }

    public function getAppTransactionId(): ?string
    {
        return $this->appTransactionId;
    }

    public function getOfferPeriod(): ?string
    {
        return $this->offerPeriod;
    }
}
