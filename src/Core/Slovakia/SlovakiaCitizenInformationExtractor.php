<?php

namespace Reducktion\Socrates\Core\Slovakia;

use Reducktion\Socrates\Models\Citizen;
use Reducktion\Socrates\Exceptions\InvalidIdException;
use Reducktion\Socrates\Exceptions\InvalidLengthException;
use Reducktion\Socrates\Contracts\CitizenInformationExtractor;
use Reducktion\Socrates\Core\CzechRepublic\CzechRepublicIdValidator;
use Reducktion\Socrates\Core\Czechoslovakia\CzechoslovakiaCitizenInformationExtractor;

class SlovakiaCitizenInformationExtractor implements CitizenInformationExtractor
{
    public function extract(string $id): Citizen
    {
        if (! (new CzechRepublicIdValidator())->validate($id)) {
            throw new InvalidIdException();
        }

        try {
            $result = CzechoslovakiaCitizenInformationExtractor::extract($id);
        } catch (InvalidLengthException $e) {
            throw new InvalidLengthException('Slovakian RC', $e->getRequiredCharacters(), $e->getGivenCharacters());
        }

        return $result;
    }
}
