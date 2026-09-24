variable "bucket_name" {
  description = "Name of the bucket"
  type        = string
}

variable "environment" {
  description = "Environment name (staging, prod)"
  type        = string
}

variable "kms_key_arn" {
  description = "KMS key for server-side encryption"
  type        = string
}

variable "versioning_enabled" {
  description = "Enable object versioning"
  type        = bool
  default     = true
}
