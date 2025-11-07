/* global $, prestashop */

import Grid from './components/grid/grid';
import FiltersResetExtension from "./components/grid/extension/filters-reset-extension";
import SortingExtension from "./components/grid/extension/sorting-extension";
import ReloadListExtension from "./components/grid/extension/reload-list-extension";
import ColumnTogglingExtension from "./components/grid/extension/column-toggling-extension";

const $ = window.$;

$(document).ready(() => {
  const connectionGrid = new Grid('adminconnectiongrid');
  connectionGrid.addExtension(new FiltersResetExtension());
  connectionGrid.addExtension(new SortingExtension());
  connectionGrid.addExtension(new ColumnTogglingExtension());

  const channelMappingGrid = new Grid('adminchannelmappinggrid');
  channelMappingGrid.addExtension(new FiltersResetExtension());
  channelMappingGrid.addExtension(new SortingExtension());
  channelMappingGrid.addExtension(new ColumnTogglingExtension());
});
