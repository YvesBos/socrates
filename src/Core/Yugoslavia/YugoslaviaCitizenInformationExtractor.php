<?php

namespace Reducktion\Socrates\Core\Yugoslavia;

use Carbon\Carbon;
use Reducktion\Socrates\Constants\Gender;
use Reducktion\Socrates\Exceptions\InvalidLengthException;
use Reducktion\Socrates\Exceptions\UnrecognisedPlaceOfBirthException;
use Reducktion\Socrates\Models\Citizen;

class YugoslaviaCitizenInformationExtractor
{
    public static function extract(string $id): Citizen
    {
        $id = trim($id);
        $idLength = strlen($id);

        if ($idLength !== 13) {
            throw new InvalidLengthException("got $idLength");
        }

        $gender = self::getGender($id);
        $dob = self::getDateOfBirth($id);
        $pob = self::getPlaceOfBirth($id);

        $citizen = new Citizen();
        $citizen->setGender($gender);
        $citizen->setDateOfBirth($dob);
        $citizen->setPlaceOfBirth($pob);

        return $citizen;
    }

    private static function getGender(string $id): string
    {
        return ((int) substr($id, 9, 3)) < 500 ? Gender::MALE : Gender::FEMALE;
    }

    private static function getDateOfBirth(string $id): Carbon
    {
        $day = substr($id, 0, 2);
        $month = substr($id, 2, 2);
        $year = (int) substr($id, 4, 3);

        $currentYear = (string) Carbon::now()->year;

        if ($year + 1000 < $currentYear && $year + 1000 > 1900) {
            $year += 1000;
        } else {
            $year += 2000;
        }

        return Carbon::createFromFormat('Y-m-d', "$year-$month-$day");
    }

    private static function getPlaceOfBirth(string $id): string
    {
        $pobCode = substr($id, 7, 2);

        if (! isset(YugoslaviaRegions::$regions[$pobCode])) {
            throw new UnrecognisedPlaceOfBirthException("The place of birth code provided '$pobCode' does not match any registered codes.");
        }

        return YugoslaviaRegions::$regions[$pobCode];
    }
}