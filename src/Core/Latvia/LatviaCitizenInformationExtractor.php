<?php

namespace Reducktion\Socrates\Core\Latvia;

use Carbon\Carbon;
use Reducktion\Socrates\Exceptions\InvalidIdException;
use Reducktion\Socrates\Models\Citizen;
use Reducktion\Socrates\Contracts\CitizenInformationExtractor;
use Reducktion\Socrates\Exceptions\UnsupportedOperationException;

class LatviaCitizenInformationExtractor implements CitizenInformationExtractor
{
    public function extract(string $id): Citizen
    {
        $id = $this->sanitize($id);

        if (! (new LatviaIdValidator())->validate($id)) {
            throw new InvalidIdException();
        }

        $citizen = new Citizen();

        if (substr($id, 0, 2) === '32') {
            throw new UnsupportedOperationException(
                'Latvia does not support citizen information extraction for PK issued after July 2017.'
            );
        }

        $dob = $this->getDateOfBirth($id);

        $citizen->setDateOfBirth($dob);

        return $citizen;
    }

    private function sanitize(string $id): string
    {
        $id = str_replace('-', '', $id);

        return $id;
    }

    private function getDateOfBirth(string $id): Carbon
    {
        $dateDigits = substr($id, 0, 6);
        [$day, $month, $year] = str_split($dateDigits, 2);
        $century = (int) $id[6];

        if ($century === 0) {
            $year += 1800;
        }

        if ($century === 1) {
            $year += 1900;
        }

        if ($century === 2) {
            $year += 2000;
        }

        return Carbon::createFromFormat('Y-m-d', "$year-$month-$day");
    }
}
