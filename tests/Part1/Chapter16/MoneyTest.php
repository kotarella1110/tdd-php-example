<?php

namespace TDD\Tests\Part1\Chapter16;

use PHPUnit\Framework\TestCase;
use TDD\Part1\Chapter16\Money;
use TDD\Part1\Chapter16\Bank;
use TDD\Part1\Chapter16\Sum;

class MoneyTest extends TestCase
{
    public function testMultiplecation()
    {
        $five = Money::dollar(5);
        $this->assertTrue(Money::dollar(10)->equals($five->times(2)));
        $this->assertTrue(Money::dollar(15)->equals($five->times(3)));
    }
    public function testEquality()
    {
        $this->assertTrue(Money::dollar(5)->equals(Money::dollar(5)));
        $this->assertFalse(Money::dollar(5)->equals(Money::dollar(6)));
        $this->assertFalse(Money::franc(5)->equals(Money::dollar(5)));
    }
    public function testCurrency()
    {
        $this->assertSame('USD', Money::dollar(1)->currency());
        $this->assertSame('CHF', Money::franc(1)->currency());
    }
    public function testSimpleAddition()
    {
        $five = Money::dollar(5);
        $sum = $five->plus($five);
        $bank = new Bank();
        $reduced = $bank->reduce($sum, 'USD');
        $this->assertTrue(Money::dollar(10)->equals($reduced));
    }
    public function testPlusReturnsSum()
    {
        $five = Money::dollar(5);
        $sum = $five->plus($five);
        $this->assertTrue($five->equals($sum->augend));
        $this->assertTrue($five->equals($sum->addend));
    }
    public function testReduceSum()
    {
        $sum = new Sum(Money::dollar(3), Money::dollar(4));
        $bank = new Bank();
        $result = $bank->reduce($sum, 'USD');
        $this->assertTrue(Money::dollar(7)->equals($result));
    }
    public function testReduceMoney()
    {
        $bank = new Bank();
        $result = $bank->reduce(Money::dollar(1), 'USD');
        $this->assertTrue(Money::dollar(1)->equals($result));
    }
    public function testReduceMoneyDifferentCurrency()
    {
        $bank = new Bank();
        $bank->addRate('CHF', 'USD', 2);
        $result = $bank->reduce(Money::franc(2), 'USD');
        $this->assertTrue(Money::dollar(1)->equals($result));
    }
    public function testIdentityRate()
    {
        $this->assertSame(1, (new Bank())->rate('USD', 'USD'));
    }
    public function testMixedAddition()
    {
        $fiveBucks = Money::dollar(5);
        $tenFrancs = Money::franc(10);
        $bank = new Bank();
        $bank->addRate('CHF', 'USD', 2);
        $result = $bank->reduce($fiveBucks->plus($tenFrancs), 'USD');
        $this->assertTrue(Money::dollar(10)->equals($result));
    }
    public function testSumPlusMoney()
    {
        $fiveBucks = Money::dollar(5);
        $tenFrancs = Money::franc(10);
        $bank = new Bank();
        $bank->addRate('CHF', 'USD', 2);
        $sum = (new Sum($fiveBucks, $tenFrancs))->plus($fiveBucks);
        $result = $bank->reduce($sum, 'USD');
        $this->assertTrue(Money::dollar(15)->equals($result));
    }
    public function testSumTimes()
    {
        $fiveBucks = Money::dollar(5);
        $tenFrancs = Money::franc(10);
        $bank = new Bank();
        $bank->addRate('CHF', 'USD', 2);
        $sum = (new Sum($fiveBucks, $tenFrancs))->times(2);
        $result = $bank->reduce($sum, 'USD');
        $this->assertTrue(Money::dollar(20)->equals($result));
    }
}