export type AnarchyBga = Bga<AnarchyPlayer, AnarchyData>;

export interface BoxSet {
  // section -> ids
  [key: string]: number[];
}

export interface PlayerBoxSet {
  // player ID -> section -> ids
  [key: number]: BoxSet;
}

export interface AnarchyPlayer extends Player {
  tent: number;

  // castle
  gate: number;
  moat: number;
  wallLeft: number;
  wallRight: number;
  wallBottom: number;
  wallTop: number;
  towerLeftTop: number;
  towerLeftBottom: number;
  towerRightTop: number;
  towerRightBottom: number;

  // pieces
  serfs: number;
  craftsmen: number;
  soldiers: number;
  patrons: number;
  knights: number;
  materials: number;
  silver: number;
  food: number;

  checkedBoxes: BoxSet;
}

export interface AnarchyData extends Gamedatas<AnarchyPlayer> {
  round: number;
  availableBoxes?: BoxSet;
  availableWalls?: (keyof AnarchyPlayer)[];
}

export interface AnarchyContext {
  data?: AnarchyData;
  bga?: AnarchyBga;
  locked: boolean;
}

export const ctx: AnarchyContext = $state({ locked: false });

/**
 * Wraps bga.actions.performAction, but sets the lock in the context around it,
 * so the UI can react by locking.
 */
export async function performAction(action: string, args?: object) {
  ctx.locked = true;
  try {
    return await ctx.bga!.actions.performAction(action, args);
  } finally {
    ctx.locked = false;
  }
}

export interface BoxRewardArgs {
  player_id: number; // forced to be underscored by the framework
  boxSection: string;
  boxId: number;
  newAvailable: number[];
}

export interface PlayerCounterArgs {
  inc: number;
  value: number;
  name: keyof AnarchyPlayer;
  playerId: number;
}

export function getBga(): AnarchyBga {
  return ctx.bga!;
}

export function getPlayer(playerId: number | string): AnarchyPlayer {
  return ctx.data!.players[
    typeof playerId === "string" ? parseInt(playerId, 10) : playerId
  ];
}

export function getCurrentPlayer(): AnarchyPlayer {
  return ctx.data!.players[ctx.bga!.players.getCurrentPlayerId()];
}

export function getCheckedBoxes(
  playerId: number | string,
  section: string,
): number[] {
  return getPlayer(playerId).checkedBoxes[section] ?? [];
}

export function getAvailableBoxes(section: string): number[] | null {
  if (ctx.data?.availableBoxes) {
    return ctx.data.availableBoxes[section];
  }
  return null;
}
