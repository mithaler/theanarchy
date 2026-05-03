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
  leftWall: number;
  rightWall: number;
  bottomWall: number;
  topWall: number;
  towerLeftWop: number;
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

  availableBoxes?: BoxSet;
  checkedBoxes: BoxSet;
}

export interface AnarchyData extends Gamedatas<AnarchyPlayer> {
  round: number;
}

export interface AnarchyContext {
  data?: AnarchyData;
  bga?: AnarchyBga;
}

export const ctx: AnarchyContext = $state({});

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

export function getPlayer(playerId: number | string): AnarchyPlayer {
  return ctx.data!.players[
    typeof playerId === "string" ? parseInt(playerId, 10) : playerId
  ];
}

export function getCheckedBoxes(
  playerId: number | string,
  section: string,
): number[] {
  return getPlayer(playerId).checkedBoxes[section] ?? [];
}

export function getAvailableBoxes(
  playerId: number | string,
  section: string,
): number[] | null {
  const availBoxes = getPlayer(playerId).availableBoxes;
  if (availBoxes) {
    return availBoxes[section];
  }
  return null;
}
