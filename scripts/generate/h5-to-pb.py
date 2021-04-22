import tensorflow as tf
import os

from transformers import AutoTokenizer, AutoModelForSeq2SeqLM
tokenizer = AutoTokenizer.from_pretrained("Michau/t5-base-en-generate-headline")

model = AutoModelForSeq2SeqLM.from_pretrained("Michau/t5-base-en-generate-headline")
output_path = os.path.realpath(os.path.dirname(os.path.realpath(__file__)) + '/../../models/t5-generate-headline')
model.save_pretrained(output_path)  # SavedModel format
