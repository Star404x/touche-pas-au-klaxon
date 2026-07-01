<?php
/**
 * Middleware de détection de périphérique
 * 
 * Détecte le type de périphérique (mobile, tablet, desktop)
 * et le stocke en session pour adaptation du contenu.
 * 
 * @package KlaxonApp\Middleware
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Middleware;

class DeviceDetectionMiddleware extends Middleware
{
    /**
     * User agents mobiles (regex patterns)
     * @var array
     */
    private static array $mobilePatterns = [
        '/Mobile|Android|iPhone|iPad|iPod|webOS|BlackBerry|Windows Phone/i'
    ];

    /**
     * Détecte le type de périphérique
     * 
     * @return bool Succès
     */
    public function handle(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $deviceType = $this->detectDevice();
        $this->setSessionValue('device_type', $deviceType);

        return true;
    }

    /**
     * Détecte le type de périphérique
     * 
     * @return string Le type (mobile, tablet, desktop)
     */
    private function detectDevice(): string
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        // Détecte tablet
        if (preg_match('/iPad|Android(?!.*Mobile)/i', $userAgent)) {
            return 'tablet';
        }

        // Détecte mobile
        foreach (self::$mobilePatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return 'mobile';
            }
        }

        return 'desktop';
    }

    /**
     * Vérifie si le périphérique est mobile
     * 
     * @return bool
     */
    public function isMobile(): bool
    {
        return $this->getSessionValue('device_type') === 'mobile';
    }

    /**
     * Vérifie si le périphérique est tablet
     * 
     * @return bool
     */
    public function isTablet(): bool
    {
        return $this->getSessionValue('device_type') === 'tablet';
    }

    /**
     * Vérifie si le périphérique est desktop
     * 
     * @return bool
     */
    public function isDesktop(): bool
    {
        return $this->getSessionValue('device_type') === 'desktop';
    }

    /**
     * Obtient le type de périphérique
     * 
     * @return string Le type
     */
    public function getDeviceType(): string
    {
        return $this->getSessionValue('device_type', 'desktop');
    }

    /**
     * Obtient la résolution recommandée pour le périphérique
     * 
     * @return string La résolution (small, medium, large)
     */
    public function getResolution(): string
    {
        return match ($this->getDeviceType()) {
            'mobile' => 'small',
            'tablet' => 'medium',
            default => 'large',
        };
    }
}
