<?php

namespace EffectConnect\Marketplaces\Grid;

use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AbstractDoctrineQueryBuilder;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;

/**
 * Class AdminChannelMappingGridQueryBuilder
 * @package EffectConnect\Marketplaces\Grid
 */
final class AdminChannelMappingGridQueryBuilder extends AbstractDoctrineQueryBuilder
{
    /**
     * {@inheritdoc}
     */
    public function getSearchQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();
        $qb
            ->orderBy(
                $searchCriteria->getOrderBy(),
                $searchCriteria->getOrderWay()
            )
            ->setFirstResult($searchCriteria->getOffset() ?? 0)
            ->setMaxResults($searchCriteria->getLimit() ?? 20);

        foreach ($searchCriteria->getFilters() as $filterName => $filterValue) {
            if ('is_active' === $filterName) {
                boolval($filterValue) ?
                    $qb->andWhere('channel_mapping.is_active = 1') :
                    $qb->andWhere('channel_mapping.is_active = 0');
                continue;
            } elseif ('payment_module_id' === $filterName) {
                if ($filterValue > 0) {
                    $qb->andWhere('channel_mapping.order_import_id_payment_module = ' . intval($filterValue));
                }
                continue;
            } elseif ('id_connection' === $filterName) {
                if ($filterValue > 0) {
                    $qb->andWhere('connection.id_connection = ' . intval($filterValue));
                }
                continue;
            } elseif ('id_channel' === $filterName) {
                if ($filterValue > 0) {
                    $qb->andWhere('channel.id_channel = ' . intval($filterValue));
                }
                continue;
            }
            $qb->andWhere("$filterName LIKE :$filterName");
            $qb->setParameter($filterName, '%' . $filterValue . '%');
        }
        return $qb;
    }

    /**
     * {@inheritdoc}
     */
    public function getCountQueryBuilder(SearchCriteriaInterface $searchCriteria)
    {
        $qb = $this->getBaseQuery();
        $qb->select('COUNT(channel_mapping.id_channel_mapping)');

        return $qb;
    }

    /**
     * Base query is the same for both searching and counting
     *
     * @return QueryBuilder
     */
    private function getBaseQuery()
    {
        $selectArray = [
            'channel_mapping.id_channel_mapping',
            'channel_mapping.is_active',
            'channel_mapping.order_import_id_payment_module',
            'channel.ec_channel_title AS channel_title',
            'channel.ec_channel_id AS channel_id',
            'connection.name AS connection_name',
        ];

        return $this->connection
            ->createQueryBuilder()
            ->from($this->dbPrefix . 'ec_channel_mapping', 'channel_mapping')
            ->select(implode(', ', $selectArray))
            ->join(
                'channel_mapping',
                $this->dbPrefix . 'ec_connection',
                'connection',
                'channel_mapping.id_connection = connection.id_connection'
            )
            ->join(
                'channel_mapping',
                $this->dbPrefix . 'ec_channel_connection',
                'channel_connection',
                'channel_connection.id_channel = channel_mapping.id_channel AND channel_connection.id_connection = channel_mapping.id_connection'
            )
            ->join(
                'channel_mapping',
                $this->dbPrefix . 'ec_channel',
                'channel',
                'channel_mapping.id_channel = channel.id_channel'
            )
        ;
    }
}
