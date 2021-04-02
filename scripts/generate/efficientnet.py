import tensorflow as tf
import os
model = tf.keras.applications.EfficientNetB0()

output_path = os.path.realpath(os.path.dirname(os.path.realpath(__file__)) + '/../../models/efficientnet')

model.save(output_path, save_format='tf')
