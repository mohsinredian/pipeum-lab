<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TenantIsolationTest extends TestCase
{
    private array $documents = [
        ['id' => 1, 'company_id' => 'company-a'],
        ['id' => 2, 'company_id' => 'company-b'],
    ];

    private function canAccess(string $company, int $docId): bool
    {
        foreach ($this->documents as $doc) {
            if ($doc['id'] === $docId) {
                return $doc['company_id'] === $company;
            }
        }
        return false;
    }

    public function test_company_can_access_own_document(): void
    {
        $this->assertTrue($this->canAccess('company-a', 1));
    }

    public function test_company_cannot_access_other_document(): void
    {
        $this->assertFalse($this->canAccess('company-b', 1));
    }
}
