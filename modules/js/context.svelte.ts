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

export type PlayerKey = keyof AnarchyPlayer;

export interface AnarchyData extends Gamedatas<AnarchyPlayer> {
  round: number;
}

export interface AnarchyState {
  availableBoxes?: BoxSet;
  availableWalls?: PlayerKey[];
}

export interface AnarchyContext {
  data?: AnarchyData;
  bga?: AnarchyBga;
  state: AnarchyState;
  locked: boolean;
}

export const ctx: AnarchyContext = $state({ locked: false, state: {} });

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

export async function checkBox(
  section: string,
  boxId: number,
  choice?: string,
  writtenValue?: number,
) {
  // zero out the player's available boxes while performing the action so it doesn't stutter
  // the notification coming back will update it with the new options, see notif_newAvailable
  const oldAvailBoxes = ctx.state.availableBoxes;
  ctx.state.availableBoxes = undefined;

  try {
    return await performAction("actCheckBox", {
      section,
      boxId,
      choice,
      writtenValue,
    });
  } catch (e) {
    // on error, set them back so we don't leave the UI unusable
    ctx.state.availableBoxes = oldAvailBoxes;
    console.error("Error checking box", e);
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
  name: PlayerKey;
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
  if (ctx.state.availableBoxes) {
    return ctx.state.availableBoxes[section];
  }
  return null;
}
