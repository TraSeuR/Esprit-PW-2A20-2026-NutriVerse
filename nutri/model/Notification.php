<?php

class Notification {
    private ?int $id = null;
    private ?string $message = null;
    private ?string $type = null; // 'stock_low', 'price_drop', 'expiration'
    private ?int $id_related = null; // product ID
    private ?string $date_created = null;
    private ?int $is_read = 0;

    public function __construct(?string $message = null, ?string $type = null, ?int $id_related = null) {
        $this->message = $message;
        $this->type = $type;
        $this->id_related = $id_related;
    }

    public function getId(): ?int { return $this->id; }
    public function getMessage(): ?string { return $this->message; }
    public function getType(): ?string { return $this->type; }
    public function getIdRelated(): ?int { return $this->id_related; }
    public function getDateCreated(): ?string { return $this->date_created; }
    public function getIsRead(): ?int { return $this->is_read; }

    public function setId(?int $id): void { $this->id = $id; }
    public function setMessage(?string $msg): void { $this->message = $msg; }
    public function setType(?string $type): void { $this->type = $type; }
    public function setIdRelated(?int $id): void { $this->id_related = $id; }
    public function setDateCreated(?string $date): void { $this->date_created = $date; }
    public function setIsRead(?int $read): void { $this->is_read = $read; }
}
?>
