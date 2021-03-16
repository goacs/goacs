export default {
  getDevice: state => state.device,
  getParameters: state => state.parameters,
  getCachedParameters: state => state.cachedParameters,
  hasCachedParams: state => state.hasCachedParams,
  getQueuedTasks: state => state.queuedTasks,
  getDeviceTemplates: state => state.deviceTemplates,
}
