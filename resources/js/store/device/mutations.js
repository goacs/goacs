export default {
  setDevice: (state, data) => state.device = data,
  setParameters: (state, data) => state.parameters = data,
  setCachedParameters: (state, data) => state.cachedParameters = data,
  hasCachedParams: (state, data) => state.hasCachedParams = data,
  setQueuedTasks: (state, data) => state.queuedTasks = data,
  setDeviceTemplates: (state, data) => state.deviceTemplates = data,
}
