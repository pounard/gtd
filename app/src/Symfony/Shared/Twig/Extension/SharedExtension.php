<?php

declare (strict_types=1);

namespace Gtd\Symfony\Shared\Twig\Extension;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

final class SharedExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('localizeddate', [$this, 'localizedDate']),
            new TwigFilter('numpad', [$this, 'numPad']),
        ];
    }

    public function localizedDate($date, $format): ?string
    {
        if ("now" === $date || null === $date || '' === $date) {
            $date = new \DateTimeImmutable();
        }
        if (!$date instanceof \DateTimeInterface) {
            return (string) $date;
        }

        switch ($format) {
            case 'long':
                return \strtr($date->format('j F Y'), [
                    'January' => 'janvier',
                    'February' => 'février',
                    'March' => 'mars',
                    'April' => 'avril',
                    'May' => 'mai',
                    'June' => 'juin',
                    'July' => 'juillet',
                    'August' => 'août',
                    'September' => 'septembre',
                    'October' => 'octrobre',
                    'November' => 'novembre',
                    'December' => 'décembre',
                ]);

            case 'short':
            default:
                return $date->format('d/m/Y');
        }
    }

    public function numPad($input, $size = 2): ?string
    {
        return \str_pad((string) $input, $size, '0', STR_PAD_LEFT);
    }
}
