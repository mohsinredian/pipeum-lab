module "uploads" {
  source = "../../modules/s3-secure"

  bucket_name = "securelab-uploads-prod"
  environment = "prod"
  kms_key_arn = var.kms_key_arn
}
