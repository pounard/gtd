<?php

namespace AppBundle\SabreDav;

use Goat\AccountBundle\Entity\Account;
use Goat\AccountBundle\Mapper\AccountMapper;
use Sabre\DAV;
use Sabre\DAVACL\PrincipalBackend\AbstractBackend;
use Sabre\HTTP\URLUtil;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Goat\AccountBundle\Security\User\GoatUser;

/**
 * Basic principal backend that will only return the current logged-in user
 * information, for security purposes. This only serves the purpose of allowing
 * the CalDAV backend to work properly via the CalendarRoot object.
 */
class PrincipalBackend extends AbstractBackend
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AccountMapper
     */
    private $accountMapper;

    /**
     * Default constructor
     *
     * @param TokenStorageInterface $tokenStorage
     * @param AccountMapper $accountMapper
     */
    public function __construct(TokenStorageInterface $tokenStorage, AccountMapper $accountMapper)
    {
        $this->tokenStorage = $tokenStorage;
        $this->accountMapper = $accountMapper;
    }

    /**
     * Get user identifier
     *
     * @return Account
     */
    private function getUserAccount() : Account
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new AccessDeniedHttpException();
        }

        $user = $token->getUser();
        if (!$user instanceof GoatUser) {
            throw new AccessDeniedHttpException();
        }

        return $user->getAccount();
    }

    /**
     * Get account principal path
     */
    private function getAccountPath(Account $account) : string
    {
        return 'principals/' . $account->getMail();
    }

    /**
     * Build principal structure for account
     *
     * @param Account $account
     *
     * @return string[]
     */
    private function buildPrincipal(Account $account) : array
    {
        return [
            'uri' => $this->getAccountPath($account),
            '{DAV:}displayname' => $account->getUsername(),
            '{http://sabredav.org/ns}email-address' => $account->getMail(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrincipalsByPrefix($prefixPath)
    {
        if ('principals' !== $prefixPath && 'principals/' !== $prefixPath) {
            return [];
        }

        return [$this->buildPrincipal($this->getUserAccount())];
    }

    /**
     * {@inheritdoc}
     */
    public function getPrincipalByPath($path)
    {
        $account = $this->getUserAccount();

        if ($this->getAccountPath($account) !== $path) {
            throw new NotFoundHttpException();
        }

        return $this->buildPrincipal($account);
    }

    /**
     * {@inheritdoc}
     */
    public function updatePrincipal($path, DAV\PropPatch $propPatch)
    {
        throw new AccessDeniedHttpException("This DAV backend is read-only");
    }

    /**
     * This method is used to search for principals matching a set of
     * properties.
     *
     * This search is specifically used by RFC3744's principal-property-search
     * REPORT.
     *
     * The actual search should be a unicode-non-case-sensitive search. The
     * keys in searchProperties are the WebDAV property names, while the values
     * are the property values to search on.
     *
     * By default, if multiple properties are submitted to this method, the
     * various properties should be combined with 'AND'. If $test is set to
     * 'anyof', it should be combined using 'OR'.
     *
     * This method should simply return an array with full principal uri's.
     *
     * If somebody attempted to search on a property the backend does not
     * support, you should simply return 0 results.
     *
     * You can also just return 0 results if you choose to not support
     * searching at all, but keep in mind that this may stop certain features
     * from working.
     *
     * @param string $prefixPath
     * @param array $searchProperties
     * @param string $test
     * @return array
     */
    public function searchPrincipals($prefixPath, array $searchProperties, $test = 'allof')
    {
        if (count($searchProperties) == 0) return [];    //No criteria

        $query = 'SELECT uri FROM ' . $this->tableName . ' WHERE ';
        $values = [];
        foreach ($searchProperties as $property => $value) {
            switch ($property) {
                case '{DAV:}displayname' :
                    $column = "displayname";
                    break;
                case '{http://sabredav.org/ns}email-address' :
                    $column = "email";
                    break;
                default :
                    // Unsupported property
                    return [];
            }
            if (count($values) > 0) $query .= (strcmp($test, "anyof") == 0 ? " OR " : " AND ");
            $query .= 'lower(' . $column . ') LIKE lower(?)';
            $values[] = '%' . $value . '%';

        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($values);

        $principals = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {

            // Checking if the principal is in the prefix
            list($rowPrefix) = URLUtil::splitPath($row['uri']);
            if ($rowPrefix !== $prefixPath) continue;

            $principals[] = $row['uri'];

        }

        return $principals;
    }

    /**
     * {@inheritdoc}
     */
    public function findByUri($uri, $principalPrefix)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupMemberSet($principal)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupMembership($principal)
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setGroupMemberSet($principal, array $members)
    {
        throw new AccessDeniedHttpException("This DAV backend is read-only");
    }
}
